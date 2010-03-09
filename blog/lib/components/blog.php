<?php
class components_Blog extends k_Component {
  protected $gateway;
  protected $templates;
  protected $form;

  /**
   * Constructor
   *
   * Relies on dependency injection handled by bucket.
   * @see www/index.php
   * @see lib/applicationfactory.php
   *
   * @param object $datasource
   * @param objct $templates
   *
   * @return void
   */
  function __construct(model_BlogGateway $datasource, k_TemplateFactory $templates) {
    $this->datasource = $datasource;
    $this->templates = $templates;
  }
  function execute() {
    return $this->wrap(parent::execute());
  }
  function wrapHtml($content) {
    $t = $this->templates->create("wrapper");
    return
      $t->render(
        $this,
        array(
      	  'href' => $this->url(),
          'title' => "index",
          'content' => $content
        ));
  }

  function renderHtml() {
    $resultset = Array();
    $results = $this->datasource->select('published', 'desc');
    $model = Array(
      'resultset' => $results
    );
    $tpl = $this->templates->create('blog/index');
    return $tpl->render($this, $model);
  }
}
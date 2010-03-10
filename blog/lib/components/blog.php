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
  function map($name) {
      return 'components_Blog_Show';
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
    $resultset = array();
    $results = $this->datasource->select(100, 0, 'published', 'desc');
    $model = array(
      'resultset' => $results
    );
    $tpl = $this->templates->create('blog/index');
    return $tpl->render($this, $model);
  }
  function postForm() {
    $values = $this->body();
    $values['published'] = date('Y-m-d H:i:s');
    unset($values['save']);
    if ($this->form()->isValid($values)) {
        $gateway = $this->datasource;
        if (!$id = $gateway->insert($values)) {
          throw new Exception("insert failed");
        }
        // It would be proper REST to reply with 201, but browsers doesn't understand that
      return new k_SeeOther($this->url($id, array('flare' => 'Created')));
    }
    return $this->render();
  }
  function renderHtmlCreate() {
    return $this->form()->render();
  }
  function form() {
    if (!isset($this->form)) {
      require_once 'Zend/Form.php';
      require_once 'Zend/View.php';
      $form = new Zend_Form();
      $form->setAction($this->url());
      $form->setMethod('post');

      $identifier = $form->createElement('text', 'name');
      $identifier->setLabel("Identifier");
      $identifier->addValidator('alnum');
      $identifier->addValidator('regex', false, array('/^[a-z]+/'));
      $identifier->setRequired(true);
      $identifier->addFilter('StringToLower');

      $title = $form->createElement('text', 'title');
      $title->setLabel("Title");
      $title->setRequired(true);

      $excerpt = $form->createElement('text', 'excerpt');
      $excerpt->setLabel("Excerpt");
      $excerpt->setRequired(true);

      $content = $form->createElement('text', 'content');
      $content->setLabel("Content");
      $content->setRequired(true);

      // Add elements to form:
      $form->addElement($identifier);
      $form->addElement($title);
      $form->addElement($excerpt);
      $form->addElement($content);
      // use addElement() as a factory to create 'Login' button:
      $form->addElement('submit', 'save', array('label' => 'Save'));

      // Since we're using this outside ZF, we need to supply a default view:
      $form->setView(new Zend_View());

      $this->form = $form;
    }
    return $this->form;
  }

}
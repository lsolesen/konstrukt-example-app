<?php
class components_Blog_Show extends k_Component
{
  protected $template;
  protected $datasource;
  function __construct(model_BlogGateway $datasource, k_TemplateFactory $template) {
    $this->template = $template;
    $this->datasource = $datasource;
  }
  function getDatasource() {
    return $this->datasource;
  }

  function getModel() {
    $res = $this->getDatasource()->fetch(array($this->getDatasource()->getPKey() => $this->name()));
    return $res;
  }
  function execute() {
      return $this->wrap(parent::execute());
  }
  function wrapHtml($content) {
    $values = $this->getModel();
    $model = array(
      'href' => $this->url(),
      'title' => $values['name'],
      'content' => $content,
    );
    $tpl = $this->template->create('wrapper');
    return $tpl->render($this, $model);
  }
  function dispatch()
  {
    if (is_null($this->getModel())) {
      throw new k_PageNotFound();
    }
    return parent::dispatch();
  }

  function renderHtml() {
    $record = $this->getModel();
    if (is_null($record)) {
      throw new k_PageNotFound();
    }
    $tpl = $this->template->create('blog/show');
    return $tpl->render($this);
  }
  function renderHtmlEdit() {
      $this->context->form()->setDefaults($this->getModel());
      return $this->context->form()->render();

  }

  function postForm(){
    $values = $this->body();
    unset($values['save']);
    if ($this->form()->isValid($values)) {
        $gateway = $this->datasource;
        if (!$gateway->update($values, array($this->datasource->getPKey() => $this->body($this->datasource->getPKey())))) {
          throw new Exception("update failed");
        }
        // It would be proper REST to reply with 201, but browsers doesn't understand that
      return new k_SeeOther($this->url(null, array('flare' => 'Updated')));
    }
    return $this->render();
  }

  function renderHtmlDelete(){
    $value = $this->getModel();
    $this->datasource->delete(array($this->datasource->getPKey() => $value[$this->datasource->getPKey()]));
    return new k_SeeOther($this->context->url());
  }

  function DELETE() {
  }
}

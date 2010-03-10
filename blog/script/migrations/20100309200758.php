#!/usr/bin/env php
<?php
require_once(dirname(__FILE__) . '/../../config/global.inc.php');
$container = create_container();
$db = $container->create('pdoext_Connection');
$db->exec('drop table if exists blogentries');
$db->exec('CREATE TABLE blogentries (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  published datetime NOT NULL,
  title varchar(255) NOT NULL,
  excerpt text NOT NULL,
  content longtext NOT NULL,
  PRIMARY KEY (id)
);');




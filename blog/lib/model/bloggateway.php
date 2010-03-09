<?php
class model_BlogGateway extends pdoext_TableGateway {
    function __construct(pdoext_Connection $pdo) {
        return parent::__construct('blogentries', $pdo);
    }
}
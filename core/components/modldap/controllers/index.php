<?php
/**
 * @package modldap
 * @subpackage controllers
 */
require_once dirname(dirname(__FILE__)).'/model/modldap/modldap.class.php';
$modldap = new ModLDAP($modx);
return $modldap->initialize('mgr');
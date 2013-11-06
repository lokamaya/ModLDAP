<?php
/**
 * @package activedirectoryx
 * @subpackage controllers
 */
require_once dirname(dirname(__FILE__)).'/model/activedirectoryx/activedirectoryx.class.php';
$activedirectoryx = new ActiveDirectoryX($modx);
return $activedirectoryx->initialize('mgr');
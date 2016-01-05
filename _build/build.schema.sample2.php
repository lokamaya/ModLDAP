<?php
/**
 * ModLDAP
 *
 * Copyright 2016 by Zaenal Muttaqin <zaenal(#)lokamaya.com>
 *
 * This file is part of ModLDAP, which integrates LDAP
 * authentication into MODx Revolution.
 *
 * ModLDAP is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * ModLDAP is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ModLDAP; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package modldap
 */
/**
 * Build Schema script
 *
 * @package modldap
 * @subpackage build
 */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

define('PKG_NAME','ModLDAP');
define('PKG_NAME_LOWER','modldap');
define('PKG_VERSION','2.4.1');
define('PKG_RELEASE','alpha');

require_once dirname(__FILE__) . '/build.config.php';
require_once dirname(__FILE__) . '/build.properties.php';
//require_once dirname(dirname(__FILE__)) . '/config.core.php';
include_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx= new modX();
$modx->initialize('mgr');
$modx->loadClass('transport.modPackageBuilder','',false, true);
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

$root = dirname(dirname(__FILE__)).'/';
$sources = array(
    'root' => $root,
    'core' => $root.'core/components/'.PKG_NAME_LOWER.'/',
    'model' => $root.'core/components/'.PKG_NAME_LOWER.'/model/',
    //'assets' => $root.'assets/components/'.PKG_NAME_LOWER.'/',
);

$manager= $modx->getManager();
$generator= $manager->getGenerator();
$generator->classTemplate= <<<EOD
<?php
/**
 * ModLDAP
 *
 * Copyright 2016 by Zaenal Muttaqin <zaenal(#)lokamaya.com>
 *
 * This file is part of ModLDAP, which integrates LDAP
 * authentication into MODx Revolution.
 *
 * ModLDAP is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * ModLDAP is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ModLDAP; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
*/
class [+class+] extends [+extends+] {}
?>
EOD;
    $generator->platformTemplate= <<<EOD
<?php
/**
 * ModLDAP
 *
 * Copyright 2016 by Zaenal Muttaqin <zaenal(#)lokamaya.com>
 *
 * This file is part of ModLDAP, which integrates LDAP
 * authentication into MODx Revolution.
 *
 * ModLDAP is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * ModLDAP is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ModLDAP; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
*/
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\\\', '/') . '/[+class-lowercase+].class.php');
class [+class+]_[+platform+] extends [+class+] {}
?>
EOD;
    $generator->mapHeader= <<<EOD
<?php
/**
 * ModLDAP
 *
 * Copyright 2016 by Zaenal Muttaqin <zaenal(#)lokamaya.com>
 *
 * This file is part of ModLDAP, which integrates LDAP
 * authentication into MODx Revolution.
 *
 * ModLDAP is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * ModLDAP is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ModLDAP; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
*/
EOD;

$generator->parseSchema($sources['model'] . 'schema/'.PKG_NAME_LOWER.'.mysql.schema.xml', $sources['model']);

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);
echo "\nExecution time: {$totalTime}\n";
exit ();
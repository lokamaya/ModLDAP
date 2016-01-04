<?php
/**
 * ModLDAP
 *
 * Copyright 2010 by Shaun McCormick <shaun@modx.com>
 * Modified in 2016 by Zaenal Muttaqin <zaenal@lokamaya.com>
 *
 * This file is part of ModLDAP, which integrates LDAP authentication
 * into MODx Revolution.
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
 * @package ModLDAP
 */
/**
 * Add events to ModLDAP plugin
 * 
 * @package ModLDAP
 * @subpackage build
 */
$events = array();

$events['OnManagerAuthentication']= $modx->newObject('modPluginEvent');
$events['OnManagerAuthentication']->fromArray(array(
    'event' => 'OnManagerAuthentication',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);
$events['OnWebAuthentication']= $modx->newObject('modPluginEvent');
$events['OnWebAuthentication']->fromArray(array(
    'event' => 'OnWebAuthentication',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);
$events['OnUserNotFound']= $modx->newObject('modPluginEvent');
$events['OnUserNotFound']->fromArray(array(
    'event' => 'OnUserNotFound',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

return $events;
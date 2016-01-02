<?php
/**
 * ModLDAP
 *
 * Copyright 2010 by Shaun McCormick <shaun@modx.com>
 * Modified in 2015 by Zaenal Muttaqin <zaenal@lokamaya.com>
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
 * Handle plugin events
 * 
 * @package modldap
 */
if (!$modx->getOption('modldap.enabled', $scriptProperties, false)) return;

$modldap = $modx->getService('modldap', 'modLDAP', $modx->getOption('modldap.core_path', null, $modx->getOption('core_path') . 'components/modldap/') . 'model/modldap/', $scriptProperties);

if (!($modldap instanceof modLDAP)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[ModLDAP] Could not load ModLDAP class.');
    $modx->event->output(false);
    return;
}
$modLDAPDriver = $modldap->loadDriver();
if (!($modLDAPDriver instanceof modLDAPDriver)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[ModLDAP] Could not load ModLDAPDriver class.');
    $modx->event->output(false);
    return;
}

/* grab correct event processor */
$eventProcessor = false;
$continue = true;
switch ($modx->event->name) {
    /* authentication mgr */
    case 'OnManagerAuthentication':
        $continue = $modx->getOption('modldap.login_manager_disable', $scriptProperties, false);
        if (!$continue) {
            return;
        }
        $eventProcessor = 'onauthentication';
        break;
        
    /* authentication context */
    case 'OnWebAuthentication':
        $continue = $modx->getOption('modldap.login_web_disable', $scriptProperties, false);
        if (!$continue) {
            return;
        }
        $eventProcessor = 'onauthentication';
        break;

    /* onusernotfound */
    case 'OnUserNotFound':
        $eventProcessor = 'onusernotfound';
        break;
}

/* if found processor, load it */
if ($continue && !empty($eventProcessor)) {
    $eventProcessor = $modldap->config['eventsPath'] . $eventProcessor . '.php';

    if (file_exists($eventProcessor)) {
        include $eventProcessor;
    }
}

return;
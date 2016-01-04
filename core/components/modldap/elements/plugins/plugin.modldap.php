<?php
/**
 * ModLDAP
 *
 * Copyright 2016 by Zaenal Muttaqin <zaenal@lokamaya.com>
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
 * @package modldap
 */
/**
 * Handle plugin events
 * 
 * @package modldap
**/
if (!$modx->getOption('modldap.enabled', $scriptProperties, false)) return;

//load modLDAP class
$modLDAP = $modx->getService('modldap', 'modLDAP', $modx->getOption('modldap.core_path', null, $modx->getOption('core_path') . 'components/modldap/') . 'model/modldap/', $scriptProperties);
if (!($modLDAP instanceof modLDAP)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[ModLDAP] Could not load ModLDAP class.');
    $modx->event->output(false);
    return;
}

if ($modx->context->get('key') == 'mgr' && $modx->getOption(modLDAP::LOGIN_MANAGER_DISABLE, $scriptProperties, false)) {
    $modx->event->output(false);
    return;
} else if ($modx->context->get('key') != 'mgr' && $modx->getOption(modLDAP::LOGIN_WEB_DISABLE, $scriptProperties, false)) {
    $modx->event->output(false);
    return;
}
    

switch ($modx->event->name) {
    /* authentication mgr */
    case 'OnManagerAuthentication':
        return $modLDAP->processOnManagerAuthentication();
        break;
        
    /* authentication other context */
    case 'OnWebAuthentication':
        return $modLDAP->processOnWebAuthentication();
        break;

    /* onusernotfound */
    case 'OnUserNotFound':
        return $modLDAP->processOnUserNotFound();
        break;
}

return;
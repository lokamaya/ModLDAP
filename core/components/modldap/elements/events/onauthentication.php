<?php
/**
 * ModLDAP
 *
 * Copyright 2015 by Zaenal Muttaqin <zaenal@lokamaya.com>
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
**/
/**
 * Authenticates the user and syncs profile data via On*Authentication events
 * 
 * @package modldap
**/

$scriptProperties = $modx->event->params;
$modx->event->_output = false;

if (empty($scriptProperties['user']) || !is_object($scriptProperties['user'])) {
    $modx->event->output(false);
    return;
}

$classKey = $scriptProperties['user']->get('class_key');

/* authenticate the user */
$success = false;
$user =& $scriptProperties['user'];

if (!is_object($user) || !($user instanceof modUser)) {
    $modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP] The user specified is not a valid modUser.');
    $modx->event->output(false);
    return;
}

/* if not an LDAP user, skip */
if ($user->get('class_key') != 'modLDAPUser') {
    $username = is_object($user) ? $user->get('username') : $user;
    $modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP] User "' . $username . '" is not a modLDAPUser and therefore is being skipped.');

    return;
}

$username = $user->get('username');
$password = $scriptProperties['password'];

/* connect to modldap */
$connected = $modLDAPDriver->connect();
if (!$connected) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[ModLDAP] Could not connect via LDAP to Active Directory.');
    $modx->event->output(false);
    return;
}
/* attempt to authenticate */
if (!($modLDAPDriver->authenticate($username,$password))) {
    $modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP] Failed to authenticate "' . $username . '" with password "' . $password . '"');
    $modx->event->output(false);
    return;
}

$modLDAPUser->syncLDAP($scriptProperties, $modLDAPDriver->getLdapEntries(), false);

$modx->event->_output = true;
return;
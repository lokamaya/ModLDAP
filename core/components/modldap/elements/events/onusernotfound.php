<?php
/**
 * ModLDAP
 *
 * Copyright 2010 by Shaun McCormick <shaun@modx.com>
 * Modified in 2015 by Zaenal Muttaqin <zaenal@lokamaya.com>
 *
 * This file is part of ModLDAP, which integrates Active Directory
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
 * Handles OnUserNotFound event
 *
 * @package modldap
 */
$scriptProperties = $modx->event->params;

if (empty($scriptProperties['username'])) return;

$modx->event->_output = false;

$username = is_object($scriptProperties['user']) && $scriptProperties['user'] instanceof modUser ? $scriptProperties['user']->get('username') : $scriptProperties['username'];

/* connect to active directory */
$connected = $modLDAPDriver->connect();
if (!$connected) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[ModLDAP] Could not connect via LDAP to Active Directory.');
    $modx->event->output(false);
    return;
}

/* authenticate the user */
if ($modLDAPDriver->authenticate($username, $scriptProperties['password'])) {
    $user =& $scriptProperties['user']; // TODO : remove?
    $user = $modx->getObject('modLDAPUser', array('username' => $username));

    if (empty($user)) {
        $user = $modx->newObject('modLDAPUser');
        $user->set('username', $username);
        $user->set('active', true);
        $user->save();

        $profile = $modx->newObject('modUserProfile');
        $profile->set('internalKey', $user->get('id'));
        $profile->save();

        $user->Profile = $profile;
    } else {
        $user->getOne('Profile');
    }

    $modx->event->_output = $user;
    $modx->event->stopPropagation();
    return;
}

$modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP] Could not authenticate user: "' . $username . '" with password "' . $scriptProperties['password'] . '".');
$modx->event->_output = false;
return;

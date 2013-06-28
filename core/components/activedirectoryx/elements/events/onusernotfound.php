<?php
/**
 * ActiveDirectoryX
 *
 * Copyright 2010 by Shaun McCormick <shaun@modx.com>
 *
 * This file is part of ActiveDirectoryX, which integrates Active Directory
 * authentication into MODx Revolution.
 *
 * ActiveDirectoryX is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * ActiveDirectoryX is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ActiveDirectoryX; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package activedirectoryx
 */
/**
 * Handles OnUserNotFound event
 *
 * @package activedirectoryx
 */
$scriptProperties = $modx->event->params;

if (empty($scriptProperties['username'])) return;

$modx->event->_output = false;

$username = is_object($scriptProperties['user']) && $scriptProperties['user'] instanceof modUser ? $scriptProperties['user']->get('username') : $scriptProperties['username'];

/* connect to active directory */
$connected = $activeDirectoryXDriver->connect();
if (!$connected) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[ActiveDirectoryX] Could not connect via LDAP to Active Directory.');
    $modx->event->output(false);
    return;
}

/* authenticate the user */
if ($activeDirectoryXDriver->authenticate($username, $scriptProperties['password'])) {
    $user =& $scriptProperties['user']; // TODO : remove?
    $user = $modx->getObject('activeDirectoryXUser', array('username' => $username));

    if (empty($user)) {
        $user = $modx->newObject('activeDirectoryXUser');
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

$modx->log(modX::LOG_LEVEL_INFO, '[ActiveDirectoryX] Could not authenticate user: "' . $username . '" with password "' . $scriptProperties['password'] . '".');
$modx->event->_output = false;
return;

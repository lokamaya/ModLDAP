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
 * Authenticates the user and syncs profile data via On*Authentication events
 * 
 * @package activedirectoryx
 */
$scriptProperties = $modx->event->params;

if (empty($scriptProperties['user']) || !is_object($scriptProperties['user'])) {
    $modx->event->output(false);
    return;
}

$classKey = $scriptProperties['user']->get('class_key');

/* authenticate the user */
$success = false;
$user =& $scriptProperties['user'];

if (!is_object($user) || !($user instanceof modUser)) {
    $modx->log(modX::LOG_LEVEL_INFO, '[ActiveDirectoryX] The user specified is not a valid modUser.');
    $modx->event->output(false);
    return;
}

/* if not an AD user, skip */
if ($user->get('class_key') != 'activeDirectoryXUser') {
    $username = is_object($user) ? $user->get('username') : $user;
    $modx->log(modX::LOG_LEVEL_INFO, '[ActiveDirectoryX] User "' . $username . '" is not a activeDirectoryXUser and therefore is being skipped.');

    if ($modx->getOption('activedirectoryx.only_ad_logins', null, false)) {
        $user->set('password', '');
    }

    return;
}

$username = $user->get('username');
$password = $scriptProperties['password'];

/* connect to activedirectoryx */
$connected = $activeDirectoryXDriver->connect();
if (!$connected) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[ActiveDirectoryX] Could not connect via LDAP to Active Directory.');
    $modx->event->output(false);
    return;
}
/* attempt to authenticate */
if (!($activeDirectoryXDriver->authenticate($username,$password))) {
    $modx->log(modX::LOG_LEVEL_INFO, '[ActiveDirectoryX] Failed to authenticate "' . $user->get('username') . '" with password "' . $password . '"');
    $modx->event->output(false);
    return;
}

/* get user info */
$userData = $activeDirectoryXDriver->userInfo($username);
if (!empty($userData) && !empty($userData[0])) {
    $userData = $userData[0];
}

/* setup profile data */
if (!empty($userData) && $user instanceof activeDirectoryXUser) {
    $profile = $user->getOne('Profile');

    if (!empty($profile)) {
        $activedirectoryx->syncProfile($profile, $userData);
    }
}

/* TODO: add ability to auto-setup user settings here */

/* always auto-add users to these groups */
$autoAddUserGroups = $modx->getOption('activedirectoryx.autoadd_usergroups', null, '');
if (!empty($autoAddUserGroups)) {
    $autoAddUserGroups = explode(',', $autoAddUserGroups);

    $roles = $modx->getOption('activedirectoryx.roles_for_autoadd_usergroups', null, '');
    $defaultRole = $modx->getObject('modUserGroupRole', 1);
    if(!empty($roles)){
        $roles = explode(',', $roles);
        $rolesForAllGroups = (count($roles) >= count($autoAddUserGroups));
    }else{
        $rolesForAllGroups = false;
        $roles = array($defaultRole->name);
    }


    foreach ($autoAddUserGroups as $position => $group) {
        $group = $modx->getObject('modUserGroup', array('name' => trim($group)));

        if ($group) {
            $exists = $modx->getObject('modUserGroupMember', array(
                'user_group' => $group->get('id'),
                'member' => $user->get('id')
            ));

            if (!$exists) {
                if($rolesForAllGroups == true){
                    $role = $modx->getObject('modUserGroupRole', array('name' => trim($roles[$position])));

                    if(!$role){
                        $role = $modx->getObject('modUserGroupRole', array('name' => $defaultRole->name));
                    }
                }else{
                    $role = $modx->getObject('modUserGroupRole', array('name' => trim($roles[0])));

                    if(!$role){
                        $role = $modx->getObject('modUserGroupRole', array('name' => $defaultRole->name));
                    }
                }

                $membership = $modx->newObject('modUserGroupMember', array(
                    'user_group' => $group->get('id'),
                    'member' => $user->get('id'),
                    'role' => $role->id
                ));

                $membership->save();
            }
        }
    }
}

/* if true, auto-add users to AD groups that exist as MODx groups */
$autoAddAdGroups = $modx->getOption('activedirectoryx.autoadd_adgroups', null, true);
if (!empty($autoAddAdGroups) && !empty($userData)) {
    $adGroups = $activedirectoryx->getGroupsFromInfo($userData);

    foreach ($adGroups as $group) {
        $group = $modx->getObject('modUserGroup', array('name' => $group));

        if ($group) {
            $exists = $modx->getObject('modUserGroupMember', array(
                'user_group' => $group->get('id'),
                'member' => $user->get('id')
            ));

            if (!$exists) {
                $membership = $modx->newObject('modUserGroupMember', array(
                    'user_group' => $group->get('id'),
                    'member' => $user->get('id'),
                    'role' => 1
                ));

                $membership->save();
            }
        }
    }
}

$modx->event->_output = true;
return;
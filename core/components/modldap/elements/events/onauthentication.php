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
**/
/**
 * Authenticates the user and syncs profile data via On*Authentication events
 * 
 * @package modldap
**/

$scriptProperties = $modx->event->params;
$modx->event->_output = false;
$success = false;

if (empty($scriptProperties['user']) || !is_object($scriptProperties['user'])) {
    $modx->event->output(false);
    return;
}

/* if not an modUser */
if (!is_object($scriptProperties['user']) || !($scriptProperties['user'] instanceof modUser)) {
    $modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP:EventOnAuthentication] The user specified is not a valid modUser.');
    $modx->event->output(false);
    return;
}

/* if not an modLDAPUser, skip */
if ( ($scriptProperties['user'] instanceof modUser) || ($scriptProperties['user'] instanceof modLDAPUser) ) {
    if (($scriptProperties['user']->get('class_key') == 'modLDAPUser') ) {
        if (!($scriptProperties['user'] instanceof modLDAPUser)) {
            $scriptProperties['user'] = $modx->getObject('modLDAPUser', array('username' => $scriptProperties['username']));
        }
    } else {
        $modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP:EventOnAuthentication] User "' . $scriptProperties['user']->get('username') . '" is not a modLDAPUser and therefore is being skipped.');
        //$modx->event->output(false);
        return;
    }
} else {
    $modx->event->output(false);
    return;
}

/* authenticate the user */
$user = $scriptProperties['user'];

$username = $user->get('username');
$password = $scriptProperties['password'];

if (empty($username) || empty($password)) {
    $modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP:EventOnAuthentication] username or password was empty!');
    $modx->event->output(false);
    return;
}

/* connect to modldap */
if ( !($modLDAPDriver->connect()) ) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[ModLDAP:EventOnAuthentication] Could not connect to LDAP.');
    $modx->event->output(false);
    return;
}

/* authenticate the user */
$authenticated = ;
if ( !($modLDAPDriver->authenticate($username, $password)) ) {
    $modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP:EventOnAuthentication] Could not authenticate user: "' . $username . '" with password "*****".');
    $modx->event->output(false);
    return;
}

$user->syncLDAP($scriptProperties, $modLDAPDriver->getLdapEntries(), false);

$scriptProperties['user'] = $user;

$modx->event->output(true);
$modx->event->_output = true;
return;
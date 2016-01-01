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
 * Handles OnUserNotFound event
 *
 * @package modldap
**/

$scriptProperties = $modx->event->params;
$modx->event->_output = false;

if (empty($scriptProperties['username'])) return;

/* connect to active directory */
if ( !($modLDAPDriver->connect()) ) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[ModLDAP:EventOnUserNotFound] Could not connect to LDAP.');
    $modx->event->output(false);
    return;
}

/* authenticate the user */
if ( !($modLDAPDriver->authenticate($scriptProperties['username'], $scriptProperties['password'])) ) {
    $modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP:EventOnUserNotFound] Could not authenticate user: "' . $username . '" with password "*****".');
    $modx->event->output(false);
    return;
}

$user = $modx->newObject('modLDAPUser');
$user->syncLDAP($scriptProperties, $modLDAPDriver->getLdapEntries(), true);

$modx->event->_output = $user;
$modx->event->stopPropagation();
return;
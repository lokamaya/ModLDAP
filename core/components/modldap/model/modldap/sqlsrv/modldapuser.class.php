<?php
/**
 * ModLDAP
 *
 * Copyright 2010 by Shaun McCormick <shaun@modxcms.com>
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
 * @package modldap
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/modldapuser.class.php');
class modLDAPUser_sqlsrv extends modLDAPUser {}
?>
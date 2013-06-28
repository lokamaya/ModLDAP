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
 * Default English language file for ActiveDirectory
 *
 * @package activedirectoryx
 */
$_lang['activedirectoryx'] = 'Active Directory X';

$_lang['setting_activedirectoryx.account_suffix'] = 'Account Suffix';
$_lang['setting_activedirectoryx.account_suffix_desc'] = 'The account suffix for your domain. Usually in @forest.domain format.';
$_lang['setting_activedirectoryx.autoadd_adgroups'] = 'Auto-Add ActiveDirectory Groups';
$_lang['setting_activedirectoryx.autoadd_adgroups_desc'] = 'If true, will grab all Active Directory groups the User belongs to, and search for any matching UserGroups in MODx. If any are found, the MODx User will automatically be added to the matching MODx UserGroups.';
$_lang['setting_activedirectoryx.autoadd_usergroups'] = 'Auto-Add User Groups';
$_lang['setting_activedirectoryx.autoadd_usergroups_desc'] = 'A comma-separated list of MODx UserGroup names which the User will always be added to.';
$_lang['setting_activedirectoryx.enabled'] = 'ActiveDirectory SSO Enabled';
$_lang['setting_activedirectoryx.enabled_desc'] = 'When set to Yes, enables the ActiveDirectoryX SSO integration. If set to No, the plugin and login system will be bypassed.';
$_lang['setting_activedirectoryx.base_dn'] = 'Base DN';
$_lang['setting_activedirectoryx.base_dn_desc'] = 'The base dn for your domain. This can usually be left blank, as MODx will automatically calculate it for you.';
$_lang['setting_activedirectoryx.domain_controllers'] = 'Domain Controllers';
$_lang['setting_activedirectoryx.domain_controllers_desc'] = 'Comma-separated list of domain controllers. Specifiy multiple controllers if you would like the class to balance the LDAP queries.';
$_lang['setting_activedirectoryx.real_primarygroup'] = 'Real Primary Group';
$_lang['setting_activedirectoryx.real_primarygroup_desc'] = 'This tweak will resolve the real primary group. Setting to false will fudge "Domain Users" and is much faster. Keep in mind though that if someones primary group is NOT "Domain Users", this is obviously going to mess up the results. Related to <a href="http://support.microsoft.com/?kbid=321360" target="blank">http://support.microsoft.com/?kbid=321360</a>.';
$_lang['setting_activedirectoryx.recursive_groups'] = 'Recursive Groups';
$_lang['setting_activedirectoryx.recursive_groups_desc'] = 'When querying group memberships, do so recursively. Recommended to leave as Yes.';
$_lang['setting_activedirectoryx.use_ssl'] = 'Use SSL';
$_lang['setting_activedirectoryx.use_ssl_desc'] = 'Use SSL (LDAPS). Your AD server will need to be setup to support this. Works only if use_tls is off.';
$_lang['setting_activedirectoryx.use_tls'] = 'Use TLS';
$_lang['setting_activedirectoryx.use_tls_desc'] = 'Use TLS. Your AD server will need to be setup to support this. Works only if use_ssl is off.';

$_lang['setting_activedirectoryx.ldap_opt_referrals'] = 'Follow LDAP Referrals';
$_lang['setting_activedirectoryx.ldap_opt_referrals_desc'] = 'Whether referrals should be followed by the LDAP client.';
$_lang['setting_activedirectoryx.ldap_opt_timelimit'] = 'LDAP Timeout Limit';
$_lang['setting_activedirectoryx.ldap_opt_timelimit_desc'] = 'Maximum number of seconds to wait for LDAP server when doing search results.';
$_lang['setting_activedirectoryx.ldap_opt_protocol_version'] = 'LDAP Protocol Version';
$_lang['setting_activedirectoryx.ldap_opt_protocol_version_desc'] = 'LDAP protocol version to use (V2 or V3)';
$_lang['setting_activedirectoryx.ldap_opt_ssl_port'] = 'LDAP SSL Port';
$_lang['setting_activedirectoryx.ldap_opt_ssl_port_desc'] = 'When using SSL, the SSL port to connect to.';

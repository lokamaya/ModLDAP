<?php
/**
 * ModLDAP
 *
 * Copyright 2010 by Shaun McCormick <shaun@modx.com>
 * Modified in 2015 by Zaenal Muttaqin <zaenal@lokamaya.com>
 *
 * This file is part of ModLDAP, which integrates OpenLDAP
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
 * Default English language file for OpenLDAP
 *
 * @package modldap
 */
$_lang['modldap'] = 'OpenLDAP';

//$_lang['setting_modldap.account_suffix'] = 'Account Suffix';
//$_lang['setting_modldap.account_suffix_desc'] = 'The account suffix for your domain. Usually in @forest.domain format.';
$_lang['setting_modldap.enabled'] = 'Enabled/Disable LDAP';
$_lang['setting_modldap.enabled_desc'] = 'When set to Yes, enables the ModLDAP SSO integration. If set to No, will be using default MODx users.';
$_lang['setting_modldap.disable_manager'] = 'Disable LDAP for Manager';
$_lang['setting_modldap.disable_manager_desc'] = 'Enable/Disable LDAP for Manager context.';
$_lang['setting_modldap.disable_web'] = 'Disable LDAP for Website';
$_lang['setting_modldap.disable_web_desc'] = 'Enable/Disable LDAP for others context.';
$_lang['setting_modldap.only_ad_logins'] = 'Only OpenLDAP logins';
$_lang['setting_modldap.only_ad_logins_desc'] = 'Allow login only from OpenLDAP. Warning: If set to Yes, standard MODX users can not log on.';

$_lang['setting_modldap.admin_username'] = 'Admin Username';
$_lang['setting_modldap.admin_username_desc'] = 'User name for LDAP admin user with permissions to list all users.';
$_lang['setting_modldap.admin_password'] = 'Admin Password';
$_lang['setting_modldap.admin_password_desc'] = 'Password for LDAP admin user with permissions to list all users.';

$_lang['setting_modldap.add_ldap_user_to_modx'] = 'Add LDAP User';
$_lang['setting_modldap.add_ldap_user_to_modx_desc'] = 'Add LDAP user to MODx (only if the same username not exists).';
$_lang['setting_modldap.autoadd_usergroups'] = 'Default MODx User Groups';
$_lang['setting_modldap.autoadd_usergroups_desc'] = 'A comma-separated list of MODx UserGroup names will be added to when LDAP user added to MODx. ';
$_lang['setting_modldap.roles_for_autoadd_usergroups'] = 'Auto add MODx roles to User Groups';
$_lang['setting_modldap.roles_for_autoadd_usergroups_desc'] = 'Comma separated list of roles for user groups. If empty, member role is added. If only 1 role is filled, all user groups will be added with this role. If same count of roles as user groups are added, group will be added with role on same position.';
$_lang['setting_modldap.maps_fields'] = 'Maps MODx to LDAP';
$_lang['setting_modldap.maps_fields_desc'] = 'Maps MODx user fields to LDAP field separated by line.';

//$_lang['setting_modldap.real_primarygroup'] = 'Real Primary Group';
//$_lang['setting_modldap.real_primarygroup_desc'] = 'This tweak will resolve the real primary group. Setting to false will fudge "Domain Users" and is much faster. Keep in mind though that if someones primary group is NOT "Domain Users", this is obviously going to mess up the results. Related to <a href="http://support.microsoft.com/?kbid=321360" target="_blank">http://support.microsoft.com/?kbid=321360</a>.';
//$_lang['setting_modldap.recursive_groups'] = 'Recursive Groups';
//$_lang['setting_modldap.recursive_groups_desc'] = 'When querying group memberships, do so recursively. Recommended to leave as Yes.';
$_lang['setting_modldap.autoadd_ldap_groups'] = 'Auto-Add LDAP Groups to MODx';
$_lang['setting_modldap.autoadd_ldap_groups_desc'] = 'If true, will grab all OpenLDAP groups the User belongs to, and search for any matching UserGroups in MODx. If any are found, the MODx User will automatically be added to the matching MODx UserGroups.';
$_lang['setting_modldap.autoadd_ldap_regex_groups'] = 'LDAP Group(s) regex';
$_lang['setting_modldap.autoadd_ldap_regex_groups_desc'] = 'Get LDAP groups using regex: <b>cn\=([^,]+).*"</b>. Leave blank if LDAP groups not required regex searching.';

$_lang['setting_modldap.domain_controllers'] = 'Domain Controllers';
$_lang['setting_modldap.domain_controllers_desc'] = 'Comma-separated list of domain controllers. Specifiy multiple controllers if you would like the class to balance the LDAP queries. See <a href="http://php.net/manual/en/function.ldap-connect.php" target="_blank">http://php.net/manual/en/function.ldap-connect.php</a>.';
$_lang['setting_modldap.connection_type'] = 'LDAP Connection Type';
$_lang['setting_modldap.connection_type_desc'] = 'Options: SSL, TLS or (blank). Blank means normal.';
$_lang['setting_modldap.ssl_port'] = 'LDAP SSL Port';
$_lang['setting_modldap.ssl_port_desc'] = 'When using SSL, the SSL port to connect to. Default port: 636.';


$_lang['setting_modldap.format_ldap_bind'] = 'Bind Format';
$_lang['setting_modldap.format_ldap_bind_desc'] = 'Some LDAP required specific format, such as: "{username}@domain.tld" or "uid:{username},dc=domain.tld" or simply "{username}". See <a href="http://php.net/manual/en/function.ldap-bind.php" target="_blank">http://php.net/manual/en/function.ldap-bind.php</a>.';
$_lang['setting_modldap.format_ldap_search_basedn'] = 'Search Format: BaseDN';
$_lang['setting_modldap.format_ldap_search_basedn_desc'] = 'Search LDAP tree using BaseDN. See <a href="http://php.net/manual/en/function.ldap-search.php" target="_blank">http://php.net/manual/en/function.ldap-search.php</a>.';
$_lang['setting_modldap.format_ldap_search_filter'] = 'Search Format: Filter';
$_lang['setting_modldap.format_ldap_search_filter_desc'] = 'Search LDAP tree using Filter. See <a href="http://php.net/manual/en/function.ldap-search.php" target="_blank">http://php.net/manual/en/function.ldap-search.php</a>.';
$_lang['setting_modldap.format_ldap_search_attributes'] = 'Search Format: Attributes';
$_lang['setting_modldap.format_ldap_search_attributes_desc'] = 'Comma separated attributs to search (output). See <a href="http://php.net/manual/en/function.ldap-search.php" target="_blank">http://php.net/manual/en/function.ldap-search.php</a>.';

$_lang['setting_modldap.ldap_opt_protocol_version'] = 'LDAP Protocol Version';
$_lang['setting_modldap.ldap_opt_protocol_version_desc'] = 'LDAP protocol version to use (V2 or V3). See <a href="http://php.net/manual/en/function.ldap-set-option.php" target="_blank">http://php.net/manual/en/function.ldap-set-option.php</a>.';
$_lang['setting_modldap.ldap_opt_referrals'] = 'Follow LDAP Referrals';
$_lang['setting_modldap.ldap_opt_referrals_desc'] = 'Whether referrals should be followed by the LDAP client. See <a href="http://php.net/manual/en/function.ldap-set-option.php" target="_blank">http://php.net/manual/en/function.ldap-set-option.php</a>.';
$_lang['setting_modldap.ldap_opt_network_timeout'] = 'LDAP Network Timeout';
$_lang['setting_modldap.ldap_opt_network_timeout_desc'] = 'Maximum number of seconds to wait for network when doing search results. See <a href="http://php.net/manual/en/function.ldap-set-option.php" target="_blank">http://php.net/manual/en/function.ldap-set-option.php</a>.';
$_lang['setting_modldap.ldap_opt_timelimit'] = 'LDAP Timeout Limit';
$_lang['setting_modldap.ldap_opt_timelimit_desc'] = 'Maximum number of seconds to wait for LDAP server when doing search results. See <a href="http://php.net/manual/en/function.ldap-set-option.php" target="_blank">http://php.net/manual/en/function.ldap-set-option.php</a>.';
$_lang['setting_modldap.ldap_opt_debug'] = 'LDAP Debug';
$_lang['setting_modldap.ldap_opt_debug_desc'] = 'Enable/disable debug. 0=disable, 7=enable. See php.net manual for mor information.';

//$_lang['setting_modldap.base_dn'] = 'Base DN';
//$_lang['setting_modldap.base_dn_desc'] = 'The base dn for your domain. This can usually be left blank, as MODx will automatically calculate it for you.';

//$_lang['setting_modldap.ldap_opt_ssl_port'] = 'LDAP SSL Port';
//$_lang['setting_modldap.ldap_opt_ssl_port_desc'] = 'When using SSL, the SSL port to connect to.';
//$_lang['setting_modldap.ldap_opt_connection_type'] = 'LDAP Connection TYpe';
//$_lang['setting_modldap.ldap_opt_connection_type_desc'] = 'Options: SSL, TLS or (blank). Blank means normal.';

//$_lang['setting_modldap.use_ssl'] = 'Use SSL';
//$_lang['setting_modldap.use_ssl_desc'] = 'Use SSL (LDAPS). Your AD server will need to be setup to support this. Works only if use_tls is off.';
//$_lang['setting_modldap.use_tls'] = 'Use TLS';
//$_lang['setting_modldap.use_tls_desc'] = 'Use TLS. Your AD server will need to be setup to support this. Works only if use_ssl is off.';

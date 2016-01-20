<?php
/**
 * ModLDAP
 *
 * Copyright 2016 by Zaenal Muttaqin <zaenal(#)lokamaya.com>
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
 * Default English language file for OpenLDAP
 *
 * @package modldap
 */
$_lang['modldap'] = 'ModLDAP';
$_lang['modldapuser'] = 'modLDAPUser';
$_lang['modldapdriver'] = 'modLDAPDriver';

/* Administration */
$_lang['setting_modldap.enabled']                   = 'LDAP Enabled';
$_lang['setting_modldap.enabled_desc']              = 'When set to Yes, enables the ModLDAP SSO integration. If set to No, will be using default MODx users.';
$_lang['setting_modldap.login_manager_disable']     = 'Disable LDAP for Manager';
$_lang['setting_modldap.login_manager_disable_desc']= 'Disable LDAP for Manager context.';
$_lang['setting_modldap.login_web_disable']         = 'Disable LDAP for Website';
$_lang['setting_modldap.login_web_disable_desc']    = 'Disable LDAP for others context.';
$_lang['setting_modldap.update_ldap']               = 'Enabled Update to LDAP';
$_lang['setting_modldap.update_ldap_desc']          = 'When set to Yes, MODx will try to update LDAP <em>(currently not aplicable)</em>.)';

/* Connection */
$_lang['setting_modldap.domain_controllers']            = 'Domain Controllers';
$_lang['setting_modldap.domain_controllers_desc']       = 'Comma-separated list of domain controllers. Specifiy multiple controllers if you would like the class to balance the LDAP queries. See <a href="http://php.net/manual/en/function.ldap-connect.php" target="_blank">http://php.net/manual/en/function.ldap-connect.php</a>.';
$_lang['setting_modldap.connection_type']               = 'LDAP Connection Type';
$_lang['setting_modldap.connection_type_desc']          = 'Options: SSL, TLS or (blank). Blank means normal.';
$_lang['setting_modldap.ssl_port']                      = 'LDAP SSL Port';
$_lang['setting_modldap.ssl_port_desc']                 = 'When using SSL, the SSL port to connect to. Default port: 636.';

$_lang['setting_modldap.ldap_opt_protocol_version']     = 'LDAP Protocol Version';
$_lang['setting_modldap.ldap_opt_protocol_version_desc']= 'LDAP protocol version to use (V2 or V3). See <a href="http://php.net/manual/en/function.ldap-set-option.php" target="_blank">http://php.net/manual/en/function.ldap-set-option.php</a>.';
$_lang['setting_modldap.ldap_opt_referrals']            = 'Follow LDAP Referrals';
$_lang['setting_modldap.ldap_opt_referrals_desc']       = 'Whether referrals should be followed by the LDAP client. See <a href="http://php.net/manual/en/function.ldap-set-option.php" target="_blank">http://php.net/manual/en/function.ldap-set-option.php</a>.';
$_lang['setting_modldap.ldap_opt_network_timeout']      = 'LDAP Network Timeout';
$_lang['setting_modldap.ldap_opt_network_timeout_desc'] = 'Maximum number of seconds to wait for network when doing search results. See <a href="http://php.net/manual/en/function.ldap-set-option.php" target="_blank">http://php.net/manual/en/function.ldap-set-option.php</a>.';
$_lang['setting_modldap.ldap_opt_timelimit']            = 'LDAP Timeout Limit';
$_lang['setting_modldap.ldap_opt_timelimit_desc']       = 'Maximum number of seconds to wait for LDAP server when doing search results. See <a href="http://php.net/manual/en/function.ldap-set-option.php" target="_blank">http://php.net/manual/en/function.ldap-set-option.php</a>.';
$_lang['setting_modldap.ldap_opt_debug']                = 'LDAP Debug';
$_lang['setting_modldap.ldap_opt_debug_desc']           = 'Enable/disable debug. 0=disable, 7=enable. See php.net manual for mor information.';

/* Data */
$_lang['setting_modldap.format_ldap_bind']                  = 'Bind Format';
$_lang['setting_modldap.format_ldap_bind_desc']             = 'Some LDAP required specific format, such as: "%username%@domain.tld" or simply "%username%" (%username% will be replaced by username, %password% will be replaced by password) See <a href="http://php.net/manual/en/function.ldap-bind.php" target="_blank">http://php.net/manual/en/function.ldap-bind.php</a>.';
$_lang['setting_modldap.format_ldap_search_basedn']         = 'Search Format: BaseDN';
$_lang['setting_modldap.format_ldap_search_basedn_desc']    = 'Search LDAP tree using BaseDN. See <a href="http://php.net/manual/en/function.ldap-search.php" target="_blank">http://php.net/manual/en/function.ldap-search.php</a>.';
$_lang['setting_modldap.format_ldap_search_filter']         = 'Search Format: Filter';
$_lang['setting_modldap.format_ldap_search_filter_desc']    = 'Search LDAP tree using Filter. See <a href="http://php.net/manual/en/function.ldap-search.php" target="_blank">http://php.net/manual/en/function.ldap-search.php</a>.';

/* User */
$_lang['setting_modldap.autoadd_usergroups']            = 'Auto Add MODx UserGroup';
$_lang['setting_modldap.autoadd_usergroups_desc']       = 'Enable/disable to add MODx UserGroup to LDAP user.';
$_lang['setting_modldap.autoadd_usergroups_name']       = 'Default MODx UserGroup';
$_lang['setting_modldap.autoadd_usergroups_name_desc']  = 'A comma-separated list of MODx UserGroup names will be added to when LDAP user added to MODx. ';
$_lang['setting_modldap.autoadd_usergroups_role']       = 'Auto add MODx roles to User Groups';
$_lang['setting_modldap.autoadd_usergroups_role_desc']  = 'Comma separated list of roles for user groups. If empty, member role is added. If only 1 role is filled, all user groups will be added with this role. If same count of roles as user groups are added, group will be added with role on same position.';

$_lang['setting_modldap.ldap_group_add']                = 'Auto-Add LDAP Groups to MODx';
$_lang['setting_modldap.ldap_group_add_desc']           = 'If true, will grab all LDAP groups the User belongs to, and search for any matching UserGroups in MODx. If any are found, the MODx User will automatically be added to the matching MODx UserGroups.';
$_lang['setting_modldap.ldap_group_field']              = 'LDAP Group Field';
$_lang['setting_modldap.ldap_group_field_desc']         = 'LDAP field that store group name. Default: "memberof"';
$_lang['setting_modldap.ldap_group_role']               = 'Role for LDAP groups';
$_lang['setting_modldap.ldap_group_role_desc']          = 'Role for LDAP groups added to MODx. Default: "Member"';
$_lang['setting_modldap.format_ldap_groups']            = 'LDAP Group(s) regex';
$_lang['setting_modldap.format_ldap_groups_desc']       = 'Get LDAP groups using regex: <b>"cn\=([^,]+).*"</b>. Leave blank if LDAP groups not required regex parsing.';

/* LDAP Fieldname */
$_lang['setting_modldap.field_fullname']        = 'Field: Fullname';
$_lang['setting_modldap.field_fullname_desc']   = 'LDAP fieldname for fullname';
$_lang['setting_modldap.field_email']           = 'Field: Email';
$_lang['setting_modldap.field_email_desc']      = 'LDAP fieldname for email';
$_lang['setting_modldap.field_phone']           = 'Field: Phone';
$_lang['setting_modldap.field_phone_desc']      = 'LDAP fieldname for phone';
$_lang['setting_modldap.field_mobilephone']     = 'Field: Mobilephone';
$_lang['setting_modldap.field_mobilephone_desc']= 'LDAP fieldname for mobilephone';
$_lang['setting_modldap.field_dob']             = 'Field: Dob';
$_lang['setting_modldap.field_dob_desc']        = 'LDAP fieldname for dob';
$_lang['setting_modldap.field_gender']          = 'Field: Gender';
$_lang['setting_modldap.field_gender_desc']     = 'LDAP fieldname for gender';
$_lang['setting_modldap.field_address']         = 'Field: Address';
$_lang['setting_modldap.field_address_desc']    = 'LDAP fieldname for address';
$_lang['setting_modldap.field_country']         = 'Field: Country';
$_lang['setting_modldap.field_country_desc']    = 'LDAP fieldname for country';
$_lang['setting_modldap.field_city']            = 'Field: City';
$_lang['setting_modldap.field_city_desc']       = 'LDAP fieldname for city';
$_lang['setting_modldap.field_state']           = 'Field: State';
$_lang['setting_modldap.field_state_desc']      = 'LDAP fieldname for state';
$_lang['setting_modldap.field_zip']             = 'Field: Zip';
$_lang['setting_modldap.field_zip_desc']        = 'LDAP fieldname for zip';
$_lang['setting_modldap.field_fax']             = 'Field: Fax';
$_lang['setting_modldap.field_fax_desc']        = 'LDAP fieldname for fax';
$_lang['setting_modldap.field_photo']           = 'Field: Photo';
$_lang['setting_modldap.field_photo_desc']      = 'LDAP fieldname for photo';
$_lang['setting_modldap.field_comment']         = 'Field: Comment';
$_lang['setting_modldap.field_comment_desc']    = 'LDAP fieldname for comment';
$_lang['setting_modldap.field_website']         = 'Field: Website';
$_lang['setting_modldap.field_website_desc']    = 'LDAP fieldname for website';
$_lang['setting_modldap.field_memberof']        = 'Field: MemberOf';
$_lang['setting_modldap.field_memberof_desc']   = 'LDAP fieldname for groups or memberof';

/* Photo Path */
$_lang['setting_modldap.photo_path']        = 'Base Photo Path';
$_lang['setting_modldap.photo_path_desc']   = 'Path to save Photo Profile. If empty, will use default MODx /assets/components/modldap/';
$_lang['setting_modldap.photo_url']         = 'Base Photo URL';
$_lang['setting_modldap.photo_url_desc']    = 'URL to get Photo Profile. If empty, will use <em>yourdomain.com</em>/assets/components/modldap/';
$_lang['setting_modldap.photo_import_size']         = 'Max Photo Size';
$_lang['setting_modldap.photo_import_size_desc']    = 'Maximum size for photo. Default 300 pixel.';
$_lang['setting_modldap.photo_import_quality']      = 'Max Photo Size';
$_lang['setting_modldap.photo_import_quality_desc'] = 'JPEG image quality between 0-100. Default 75.';

/* Properties */
$_lang['prop_modldap.distinguish_object'] = 'Distinguish object';
$_lang['prop_modldap.ldap_username'] = 'LDAP Username';
$_lang['prop_modldap.ldap_password'] = 'LDAP Password';

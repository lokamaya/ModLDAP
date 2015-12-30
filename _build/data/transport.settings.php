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
 * Add in system settings
 * 
 * @package modldap
 * @subpackage build
 */
$settings = array();

/*
$settings['modldap.account_suffix']= $modx->newObject('modSystemSetting');
$settings['modldap.account_suffix']->fromArray(array(
    'key' => 'modldap.account_suffix',
    'value' => '@forest.local',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'ModLDAP',
),'',true,true);
*/

$settings['modldap.enabled']= $modx->newObject('modSystemSetting');
$settings['modldap.enabled']->fromArray(array(
    'key' => 'modldap.enabled',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'ModLDAP',
),'',true,true);

$settings['modldap.disable_manager']= $modx->newObject('modSystemSetting');
$settings['modldap.disable_manager']->fromArray(array(
    'key' => 'modldap.disable_manager',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'ModLDAP',
),'',true,true);

$settings['modldap.disable_web']= $modx->newObject('modSystemSetting');
$settings['modldap.disable_web']->fromArray(array(
    'key' => 'modldap.disable_web',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'ModLDAP',
),'',true,true);

$settings['modldap.only_ad_logins']= $modx->newObject('modSystemSetting');
$settings['modldap.only_ad_logins']->fromArray(array(
    'key' => 'modldap.only_ad_logins',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'ModLDAP',
),'',true,true);

/*
$settings['modldap.real_primarygroup']= $modx->newObject('modSystemSetting');
$settings['modldap.real_primarygroup']->fromArray(array(
    'key' => 'modldap.real_primarygroup',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'ModLDAP',
),'',true,true);

$settings['modldap.recursive_groups']= $modx->newObject('modSystemSetting');
$settings['modldap.recursive_groups']->fromArray(array(
    'key' => 'modldap.recursive_groups',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'ModLDAP',
),'',true,true);
*/

$settings['modldap.admin_username']= $modx->newObject('modSystemSetting');
$settings['modldap.admin_username']->fromArray(array(
        'key' => 'modldap.admin_username',
        'value' => '',
        'xtype' => 'textfield',
        'namespace' => 'modldap',
        'area' => 'LDAPAdmin',
),'',true,true);

$settings['modldap.admin_password']= $modx->newObject('modSystemSetting');
$settings['modldap.admin_password']->fromArray(array(
        'key' => 'modldap.admin_password',
        'value' => '',
        'xtype' => 'text-password',
        'namespace' => 'modldap',
        'area' => 'LDAPAdmin',
),'',true,true);

$settings['modldap.add_ldap_user_to_modx']= $modx->newObject('modSystemSetting');
$settings['modldap.add_ldap_user_to_modx']->fromArray(array(
    'key' => 'modldap.add_ldap_user_to_modx',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'ModLDAP',
),'',true,true);

$settings['modldap.autoadd_usergroups']= $modx->newObject('modSystemSetting');
$settings['modldap.autoadd_usergroups']->fromArray(array(
    'key' => 'modldap.autoadd_usergroups',
    'value' => 'LDAP',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAPAdmin',
),'',true,true);

$settings['modldap.roles_for_autoadd_usergroups']= $modx->newObject('modSystemSetting');
$settings['modldap.roles_for_autoadd_usergroups']->fromArray(array(
        'key' => 'modldap.roles_for_autoadd_usergroups',
        'value' => 'Member',
        'xtype' => 'textfield',
        'namespace' => 'modldap',
        'area' => 'LDAPAdmin',
),'',true,true);

$settings['modldap.maps_fields']= $modx->newObject('modSystemSetting');
$settings['modldap.maps_fields']->fromArray(array(
       'key' => 'modldap.maps_fields',
       'value' => "fullname=givenname\nemail=email\nphone=\nmobilephone=\ndob=\ngender=\naddress=\ncountry=\ncity=\nstate=\nzip=\nfax=\nphoto=\ncomment=\nwebsite=",
       'xtype' => 'textarea',
       'namespace' => 'modldap',
       'area' => 'LDAPAdmin',
),'',true,true);

$settings['modldap.autoadd_ldap_groups']= $modx->newObject('modSystemSetting');
$settings['modldap.autoadd_ldap_groups']->fromArray(array(
    'key' => 'modldap.autoadd_ldap_groups',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'ModLDAP',
),'',true,true);

$settings['modldap.autoadd_ldap_regex_groups']= $modx->newObject('modSystemSetting');
$settings['modldap.autoadd_ldap_regex_groups']->fromArray(array(
       'key' => 'modldap.autoadd_ldap_regex_groups',
       'value' => 'cn\=([^,]+).*',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'LDAPAdmin',
),'',true,true);

/* LDAP-format */
$settings['modldap.domain_controllers']= $modx->newObject('modSystemSetting');
$settings['modldap.domain_controllers']->fromArray(array(
    'key' => 'modldap.domain_controllers',
    'value' => 'localhost',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);

$settings['modldap.connection_type']= $modx->newObject('modSystemSetting');
$settings['modldap.connection_type']->fromArray(array(
    'key' => 'modldap.connection_type',
    'value' => 'localhost',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);

$settings['modldap.ssl_port']= $modx->newObject('modSystemSetting');
$settings['modldap.ssl_port']->fromArray(array(
    'key' => 'modldap.ssl_port',
    'value' => '636',
    'xtype' => 'numberfield',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);

$settings['modldap.format_ldap_bind']= $modx->newObject('modSystemSetting');
$settings['modldap.format_ldap_bind']->fromArray(array(
    'key' => 'modldap.format_ldap_bind',
    'value' => 'uid={username},ou=member,dc=domain,dc=tld',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);

$settings['modldap.format_ldap_search_basedn']= $modx->newObject('modSystemSetting');
$settings['modldap.format_ldap_search_basedn']->fromArray(array(
    'key' => 'modldap.format_ldap_search_basedn',
    'value' => 'dc=domain,dc=tld',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);

$settings['modldap.format_ldap_search_filter']= $modx->newObject('modSystemSetting');
$settings['modldap.format_ldap_search_filter']->fromArray(array(
    'key' => 'modldap.format_ldap_search_filter',
    'value' => '(&(objectClass=person)(uid={username}))',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);

$settings['modldap.format_ldap_search_attributes']= $modx->newObject('modSystemSetting');
$settings['modldap.format_ldap_search_attributes']->fromArray(array(
    'key' => 'modldap.format_ldap_search_attributes',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);

$settings['modldap.ldap_opt_protocol_version']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_opt_protocol_version']->fromArray(array(
    'key' => 'modldap.ldap_opt_protocol_version',
    'value' => 3,
    'xtype' => 'numberfield',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);

$settings['modldap.ldap_opt_referrals']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_opt_referrals']->fromArray(array(
    'key' => 'modldap.ldap_opt_referrals',
    'value' => 0,
    'xtype' => 'numberfield',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);

$settings['modldap.ldap_opt_network_timeout']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_opt_network_timeout']->fromArray(array(
    'key' => 'modldap.ldap_opt_network_timeout',
    'value' => 10,
    'xtype' => 'numberfield',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);

$settings['modldap.ldap_opt_timelimit']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_opt_timelimit']->fromArray(array(
    'key' => 'modldap.ldap_opt_timelimit',
    'value' => 10,
    'xtype' => 'numberfield',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);

$settings['modldap.ldap_opt_debug']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_opt_debug']->fromArray(array(
    'key' => 'modldap.ldap_opt_debug',
    'value' => 0,
    'xtype' => 'numberfield',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);

/*
$settings['modldap.base_dn']= $modx->newObject('modSystemSetting');
$settings['modldap.base_dn']->fromArray(array(
    'key' => 'modldap.base_dn',
    'value' => '',
    'xtype' => 'textarea',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);
*/

/*
$settings['modldap.ldap_opt_ssl_port']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_opt_ssl_port']->fromArray(array(
    'key' => 'modldap.ldap_opt_ssl_port',
    'value' => 636,
    'xtype' => 'numberfield',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);

$settings['modldap.ldap_opt_connection_type']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_opt_connection_type']->fromArray(array(
    'key' => 'modldap.ldap_opt_connection_type',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);

$settings['modldap.use_ssl']= $modx->newObject('modSystemSetting');
$settings['modldap.use_ssl']->fromArray(array(
    'key' => 'modldap.use_ssl',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);

$settings['modldap.use_tls']= $modx->newObject('modSystemSetting');
$settings['modldap.use_tls']->fromArray(array(
    'key' => 'modldap.use_tls',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'LDAPconnection',
),'',true,true);
*/


return $settings;
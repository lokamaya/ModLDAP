<?php
/**
 * ModLDAP
 *
 * Copyright 2010 by Zaenal Muttaqin <zaenal@lokamaya.com>
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
 */
/**
 * Add in system settings
 * 
 * @package modldap
 * @subpackage build
 */
$settings = array();

/* LDAP Administration */
$settings['modldap.enabled']= $modx->newObject('modSystemSetting');
$settings['modldap.enabled']->fromArray(array(
    'key' => 'modldap.enabled',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'LDAPadministration',
),'',true,true);

$settings['modldap.disable_manager']= $modx->newObject('modSystemSetting');
$settings['modldap.disable_manager']->fromArray(array(
    'key' => 'modldap.disable_manager',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'LDAPadministration',
),'',true,true);

$settings['modldap.disable_web']= $modx->newObject('modSystemSetting');
$settings['modldap.disable_web']->fromArray(array(
    'key' => 'modldap.disable_web',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'LDAPadministration',
),'',true,true);

/* LDAP Connection */
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

/* LDAP Data */
$settings['modldap.format_ldap_bind']= $modx->newObject('modSystemSetting');
$settings['modldap.format_ldap_bind']->fromArray(array(
    'key' => 'modldap.format_ldap_bind',
    'value' => 'uid={username},ou=member,dc=domain,dc=tld',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAPdata',
),'',true,true);

$settings['modldap.format_ldap_search_basedn']= $modx->newObject('modSystemSetting');
$settings['modldap.format_ldap_search_basedn']->fromArray(array(
    'key' => 'modldap.format_ldap_search_basedn',
    'value' => 'dc=domain,dc=tld',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAPdata',
),'',true,true);

$settings['modldap.format_ldap_search_filter']= $modx->newObject('modSystemSetting');
$settings['modldap.format_ldap_search_filter']->fromArray(array(
    'key' => 'modldap.format_ldap_search_filter',
    'value' => '(&(objectClass=person)(uid={username}))',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAPdata',
),'',true,true);

$settings['modldap.format_ldap_search_attributes']= $modx->newObject('modSystemSetting');
$settings['modldap.format_ldap_search_attributes']->fromArray(array(
    'key' => 'modldap.format_ldap_search_attributes',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAPdata',
),'',true,true);

/* User */
/*
$settings['modldap.add_ldap_user_to_modx']= $modx->newObject('modSystemSetting');
$settings['modldap.add_ldap_user_to_modx']->fromArray(array(
    'key' => 'modldap.add_ldap_user_to_modx',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'User',
),'',true,true);
*/

$settings['modldap.autoadd_usergroups']= $modx->newObject('modSystemSetting');
$settings['modldap.autoadd_usergroups']->fromArray(array(
    'key' => 'modldap.autoadd_usergroups',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'User',
),'',true,true);

$settings['modldap.autoadd_usergroups_name']= $modx->newObject('modSystemSetting');
$settings['modldap.autoadd_usergroups_name']->fromArray(array(
    'key' => 'modldap.autoadd_usergroups_name',
    'value' => 'LDAP',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'User',
),'',true,true);

$settings['modldap.autoadd_usergroups_role']= $modx->newObject('modSystemSetting');
$settings['modldap.autoadd_usergroups_role']->fromArray(array(
        'key' => 'modldap.autoadd_usergroups_role',
        'value' => 'Member',
        'xtype' => 'textfield',
        'namespace' => 'modldap',
        'area' => 'User',
),'',true,true);

$settings['modldap.maps_fields']= $modx->newObject('modSystemSetting');
$settings['modldap.maps_fields']->fromArray(array(
       'key' => 'modldap.maps_fields',
       'value' => "fullname=cn\nemail=email\nphone=\nmobilephone=\ndob=\ngender=\naddress=\ncountry=\ncity=\nstate=\nzip=\nfax=\nphoto=\ncomment=\nwebsite=",
       'xtype' => 'textarea',
       'namespace' => 'modldap',
       'area' => 'User',
),'',true,true);

$settings['modldap.ldap_group_add']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_group_add']->fromArray(array(
    'key' => 'modldap.ldap_group_add',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'User',
),'',true,true);

$settings['modldap.ldap_group_field']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_group_field']->fromArray(array(
    'key' => 'modldap.ldap_group_field',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'User',
),'',true,true);

$settings['modldap.ldap_group_role']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_group_role']->fromArray(array(
       'key' => 'modldap.ldap_group_role',
       'value' => 'Member',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'User',
),'',true,true);

$settings['modldap.format_ldap_groups']= $modx->newObject('modSystemSetting');
$settings['modldap.format_ldap_groups']->fromArray(array(
       'key' => 'modldap.format_ldap_groups',
       'value' => '@cn\=([^,\=]+).*@i',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'User',
),'',true,true);

return $settings;
<?php
/**
 * ModLDAP
 *
 * Copyright 2010 by Zaenal Muttaqin <zaenal(#)lokamaya.com>
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
    'area' => 'LDAP-Administration',
),'',true,true);

$settings['modldap.login_manager_disable']= $modx->newObject('modSystemSetting');
$settings['modldap.login_manager_disable']->fromArray(array(
    'key' => 'modldap.login_manager_disable',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'LDAP-Administration',
),'',true,true);

$settings['modldap.login_web_disable']= $modx->newObject('modSystemSetting');
$settings['modldap.login_web_disable']->fromArray(array(
    'key' => 'modldap.login_web_disable',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'LDAP-Administration',
),'',true,true);

/* LDAP Connection */
$settings['modldap.domain_controllers']= $modx->newObject('modSystemSetting');
$settings['modldap.domain_controllers']->fromArray(array(
    'key' => 'modldap.domain_controllers',
    'value' => 'localhost',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAP-Connection',
),'',true,true);

$settings['modldap.connection_type']= $modx->newObject('modSystemSetting');
$settings['modldap.connection_type']->fromArray(array(
    'key' => 'modldap.connection_type',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAP-Connection',
),'',true,true);

$settings['modldap.ssl_port']= $modx->newObject('modSystemSetting');
$settings['modldap.ssl_port']->fromArray(array(
    'key' => 'modldap.ssl_port',
    'value' => '636',
    'xtype' => 'numberfield',
    'namespace' => 'modldap',
    'area' => 'LDAP-Connection',
),'',true,true);

$settings['modldap.ldap_opt_protocol_version']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_opt_protocol_version']->fromArray(array(
    'key' => 'modldap.ldap_opt_protocol_version',
    'value' => 3,
    'xtype' => 'numberfield',
    'namespace' => 'modldap',
    'area' => 'LDAP-Connection',
),'',true,true);

$settings['modldap.ldap_opt_referrals']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_opt_referrals']->fromArray(array(
    'key' => 'modldap.ldap_opt_referrals',
    'value' => 0,
    'xtype' => 'numberfield',
    'namespace' => 'modldap',
    'area' => 'LDAP-Connection',
),'',true,true);

$settings['modldap.ldap_opt_network_timeout']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_opt_network_timeout']->fromArray(array(
    'key' => 'modldap.ldap_opt_network_timeout',
    'value' => 10,
    'xtype' => 'numberfield',
    'namespace' => 'modldap',
    'area' => 'LDAP-Connection',
),'',true,true);

$settings['modldap.ldap_opt_timelimit']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_opt_timelimit']->fromArray(array(
    'key' => 'modldap.ldap_opt_timelimit',
    'value' => 10,
    'xtype' => 'numberfield',
    'namespace' => 'modldap',
    'area' => 'LDAP-Connection',
),'',true,true);

$settings['modldap.ldap_opt_debug']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_opt_debug']->fromArray(array(
    'key' => 'modldap.ldap_opt_debug',
    'value' => 0,
    'xtype' => 'numberfield',
    'namespace' => 'modldap',
    'area' => 'LDAP-Connection',
),'',true,true);

/* LDAP Data */
$settings['modldap.format_ldap_bind']= $modx->newObject('modSystemSetting');
$settings['modldap.format_ldap_bind']->fromArray(array(
    'key' => 'modldap.format_ldap_bind',
    'value' => 'uid=%username%,ou=member,dc=domain,dc=tld',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAP-Parsing',
),'',true,true);

$settings['modldap.format_ldap_search_basedn']= $modx->newObject('modSystemSetting');
$settings['modldap.format_ldap_search_basedn']->fromArray(array(
    'key' => 'modldap.format_ldap_search_basedn',
    'value' => 'dc=domain,dc=tld',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAP-Parsing',
),'',true,true);

$settings['modldap.format_ldap_search_filter']= $modx->newObject('modSystemSetting');
$settings['modldap.format_ldap_search_filter']->fromArray(array(
    'key' => 'modldap.format_ldap_search_filter',
    'value' => '(&(objectClass=person)(uid=%username%))',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAP-Parsing',
),'',true,true);
$settings['modldap.format_ldap_groups']= $modx->newObject('modSystemSetting');
$settings['modldap.format_ldap_groups']->fromArray(array(
    'key' => 'modldap.format_ldap_groups',
    'value' => '@cn\=([^,\=]+).*@i',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'LDAP-Parsing',
),'',true,true);

/* User */
$settings['modldap.autoadd_usergroups']= $modx->newObject('modSystemSetting');
$settings['modldap.autoadd_usergroups']->fromArray(array(
    'key' => 'modldap.autoadd_usergroups',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'UserGroup',
),'',true,true);

$settings['modldap.autoadd_usergroups_name']= $modx->newObject('modSystemSetting');
$settings['modldap.autoadd_usergroups_name']->fromArray(array(
    'key' => 'modldap.autoadd_usergroups_name',
    'value' => 'LDAP',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'UserGroup',
),'',true,true);

$settings['modldap.autoadd_usergroups_role']= $modx->newObject('modSystemSetting');
$settings['modldap.autoadd_usergroups_role']->fromArray(array(
    'key' => 'modldap.autoadd_usergroups_role',
    'value' => 'Member',
    'xtype' => 'textfield',
    'namespace' => 'modldap',
    'area' => 'UserGroup',
),'',true,true);

$settings['modldap.ldap_group_add']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_group_add']->fromArray(array(
    'key' => 'modldap.ldap_group_add',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'modldap',
    'area' => 'UserGroup',
),'',true,true);

/*
$settings['modldap.ldap_group_role']= $modx->newObject('modSystemSetting');
$settings['modldap.ldap_group_role']->fromArray(array(
       'key' => 'modldap.ldap_group_role',
       'value' => 'Member',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserGroup',
),'',true,true);
*/




/* User Fields */
$settings['modldap.field_fullname']= $modx->newObject('modSystemSetting');
$settings['modldap.field_fullname']->fromArray(array(
       'key' => 'modldap.field_fullname',
       'value' => 'cn',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserField',
),'',true,true);

$settings['modldap.field_email']= $modx->newObject('modSystemSetting');
$settings['modldap.field_email']->fromArray(array(
       'key' => 'modldap.field_email',
       'value' => 'mail',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserField',
),'',true,true);

$settings['modldap.field_phone']= $modx->newObject('modSystemSetting');
$settings['modldap.field_phone']->fromArray(array(
       'key' => 'modldap.field_phone',
       'value' => '',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserField',
),'',true,true);

$settings['modldap.field_mobilephone']= $modx->newObject('modSystemSetting');
$settings['modldap.field_mobilephone']->fromArray(array(
       'key' => 'modldap.field_mobilephone',
       'value' => '',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserField',
),'',true,true);

$settings['modldap.field_dob']= $modx->newObject('modSystemSetting');
$settings['modldap.field_dob']->fromArray(array(
       'key' => 'modldap.field_dob',
       'value' => '',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserField',
),'',true,true);

$settings['modldap.field_gender']= $modx->newObject('modSystemSetting');
$settings['modldap.field_gender']->fromArray(array(
       'key' => 'modldap.field_gender',
       'value' => '',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserField',
),'',true,true);

$settings['modldap.field_address']= $modx->newObject('modSystemSetting');
$settings['modldap.field_address']->fromArray(array(
       'key' => 'modldap.field_address',
       'value' => '',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserField',
),'',true,true);

$settings['modldap.field_country']= $modx->newObject('modSystemSetting');
$settings['modldap.field_country']->fromArray(array(
       'key' => 'modldap.field_country',
       'value' => '',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserField',
),'',true,true);

$settings['modldap.field_city']= $modx->newObject('modSystemSetting');
$settings['modldap.field_city']->fromArray(array(
       'key' => 'modldap.field_city',
       'value' => '',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserField',
),'',true,true);

$settings['modldap.field_state']= $modx->newObject('modSystemSetting');
$settings['modldap.field_state']->fromArray(array(
       'key' => 'modldap.field_state',
       'value' => '',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserField',
),'',true,true);

$settings['modldap.field_zip']= $modx->newObject('modSystemSetting');
$settings['modldap.field_zip']->fromArray(array(
       'key' => 'modldap.field_zip',
       'value' => '',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserField',
),'',true,true);

$settings['modldap.field_fax']= $modx->newObject('modSystemSetting');
$settings['modldap.field_fax']->fromArray(array(
       'key' => 'modldap.field_fax',
       'value' => '',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserField',
),'',true,true);

$settings['modldap.field_photo']= $modx->newObject('modSystemSetting');
$settings['modldap.field_photo']->fromArray(array(
       'key' => 'modldap.field_photo',
       'value' => 'jpegphoto',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserField',
),'',true,true);

$settings['modldap.field_comment']= $modx->newObject('modSystemSetting');
$settings['modldap.field_comment']->fromArray(array(
       'key' => 'modldap.field_comment',
       'value' => '',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserField',
),'',true,true);

$settings['modldap.field_website']= $modx->newObject('modSystemSetting');
$settings['modldap.field_website']->fromArray(array(
       'key' => 'modldap.field_website',
       'value' => '',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserField',
),'',true,true);

$settings['modldap.field_memberof']= $modx->newObject('modSystemSetting');
$settings['modldap.field_memberof']->fromArray(array(
       'key' => 'modldap.field_memberof',
       'value' => '',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserField',
),'',true,true);


/* User Photo */
$settings['modldap.photo_path']= $modx->newObject('modSystemSetting');
$settings['modldap.photo_path']->fromArray(array(
       'key' => 'modldap.photo_path',
       'value' => '',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserPhoto',
),'',true,true);

$settings['modldap.photo_url']= $modx->newObject('modSystemSetting');
$settings['modldap.photo_url']->fromArray(array(
       'key' => 'modldap.photo_url',
       'value' => '',
       'xtype' => 'textfield',
       'namespace' => 'modldap',
       'area' => 'UserPhoto',
),'',true,true);

$settings['modldap.photo_import_size']= $modx->newObject('modSystemSetting');
$settings['modldap.photo_import_size']->fromArray(array(
       'key' => 'modldap.photo_import_size',
       'value' => '300',
       'xtype' => 'numberfield',
       'namespace' => 'modldap',
       'area' => 'UserPhoto',
),'',true,true);

$settings['modldap.photo_import_quality']= $modx->newObject('modSystemSetting');
$settings['modldap.photo_import_quality']->fromArray(array(
       'key' => 'modldap.photo_import_quality',
       'value' => '75',
       'xtype' => 'numberfield',
       'namespace' => 'modldap',
       'area' => 'UserPhoto',
),'',true,true);

return $settings;
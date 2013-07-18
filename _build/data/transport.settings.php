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
 * Add in system settings
 * 
 * @package activedirectoryx
 * @subpackage build
 */
$settings = array();

$settings['activedirectoryx.account_suffix']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.account_suffix']->fromArray(array(
    'key' => 'activedirectoryx.account_suffix',
    'value' => '@forest.local',
    'xtype' => 'textfield',
    'namespace' => 'activedirectoryx',
    'area' => 'ActiveDirectoryX',
),'',true,true);

$settings['activedirectoryx.enabled']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.enabled']->fromArray(array(
    'key' => 'activedirectoryx.enabled',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'activedirectoryx',
    'area' => 'ActiveDirectoryX',
),'',true,true);

$settings['activedirectoryx.only_ad_logins']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.only_ad_logins']->fromArray(array(
    'key' => 'activedirectoryx.only_ad_logins',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'activedirectoryx',
    'area' => 'ActiveDirectoryX',
),'',true,true);

$settings['activedirectoryx.autoadd_adgroups']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.autoadd_adgroups']->fromArray(array(
    'key' => 'activedirectoryx.autoadd_adgroups',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'activedirectoryx',
    'area' => 'ActiveDirectoryX',
),'',true,true);

$settings['activedirectoryx.autoadd_usergroups']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.autoadd_usergroups']->fromArray(array(
    'key' => 'activedirectoryx.autoadd_usergroups',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'activedirectoryx',
    'area' => 'ActiveDirectoryX',
),'',true,true);

$settings['activedirectoryx.base_dn']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.base_dn']->fromArray(array(
    'key' => 'activedirectoryx.base_dn',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'activedirectoryx',
    'area' => 'ActiveDirectoryX',
),'',true,true);

$settings['activedirectoryx.domain_controllers']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.domain_controllers']->fromArray(array(
    'key' => 'activedirectoryx.domain_controllers',
    'value' => '127.0.0.1',
    'xtype' => 'textfield',
    'namespace' => 'activedirectoryx',
    'area' => 'ActiveDirectoryX',
),'',true,true);

$settings['activedirectoryx.real_primarygroup']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.real_primarygroup']->fromArray(array(
    'key' => 'activedirectoryx.real_primarygroup',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'activedirectoryx',
    'area' => 'ActiveDirectoryX',
),'',true,true);

$settings['activedirectoryx.recursive_groups']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.recursive_groups']->fromArray(array(
    'key' => 'activedirectoryx.recursive_groups',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'activedirectoryx',
    'area' => 'ActiveDirectoryX',
),'',true,true);

$settings['activedirectoryx.use_ssl']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.use_ssl']->fromArray(array(
    'key' => 'activedirectoryx.use_ssl',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'activedirectoryx',
    'area' => 'ActiveDirectoryX',
),'',true,true);

$settings['activedirectoryx.use_tls']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.use_tls']->fromArray(array(
    'key' => 'activedirectoryx.use_tls',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'activedirectoryx',
    'area' => 'ActiveDirectoryX',
),'',true,true);

/* LDAP-specific settings */
$settings['activedirectoryx.ldap_opt_referrals']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.ldap_opt_referrals']->fromArray(array(
    'key' => 'activedirectoryx.ldap_opt_referrals',
    'value' => 0,
    'xtype' => 'textfield',
    'namespace' => 'activedirectoryx',
    'area' => 'LDAP',
),'',true,true);
$settings['activedirectoryx.ldap_opt_timelimit']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.ldap_opt_timelimit']->fromArray(array(
    'key' => 'activedirectoryx.ldap_opt_timelimit',
    'value' => 10,
    'xtype' => 'textfield',
    'namespace' => 'activedirectoryx',
    'area' => 'LDAP',
),'',true,true);
$settings['activedirectoryx.ldap_opt_protocol_version']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.ldap_opt_protocol_version']->fromArray(array(
    'key' => 'activedirectoryx.ldap_opt_protocol_version',
    'value' => 3,
    'xtype' => 'textfield',
    'namespace' => 'activedirectoryx',
    'area' => 'LDAP',
),'',true,true);
$settings['activedirectoryx.ldap_opt_ssl_port']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.ldap_opt_ssl_port']->fromArray(array(
    'key' => 'activedirectoryx.ldap_opt_ssl_port',
    'value' => 636,
    'xtype' => 'textfield',
    'namespace' => 'activedirectoryx',
    'area' => 'LDAP',
),'',true,true);
$settings['activedirectoryx.admin_username']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.admin_username']->fromArray(array(
        'key' => 'activedirectoryx.admin_username',
        'value' => '',
        'xtype' => 'textfield',
        'namespace' => 'activedirectoryx',
        'area' => 'LDAP Admin',
   ),'',true,true);
$settings['activedirectoryx.admin_password']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.admin_password']->fromArray(array(
        'key' => 'activedirectoryx.admin_password',
        'value' => '',
        'xtype' => 'text-password',
        'namespace' => 'activedirectoryx',
        'area' => 'LDAP Admin',
   ),'',true,true);
$settings['activedirectoryx.admin_suffix']= $modx->newObject('modSystemSetting');
$settings['activedirectoryx.admin_suffix']->fromArray(array(
         'key' => 'activedirectoryx.admin_suffix',
         'value' => '',
         'xtype' => 'textfield',
         'namespace' => 'activedirectoryx',
         'area' => 'LDAP Admin',
    ),'',true,true);

return $settings;
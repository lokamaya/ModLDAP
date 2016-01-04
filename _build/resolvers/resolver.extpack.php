<?php
/**
 * ModLDAP
 *
 * Copyright 2010 by Shaun McCormick <shaun@modx.com>
 * Modified in 2016 by Zaenal Muttaqin <zaenal@lokamaya.com>
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
 * Add modldap package path to extension_packages setting
 *
 * @package modldap
 * @subpackage build
 */
$success = true;
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        /* ensure setting is correct on install and upgrade */
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('modldap.core_path',null,$modx->getOption('core_path').'components/modldap/').'model/';
            //$modx->addPackage('modldap',$modelPath);

            $setting = $modx->getObject('modSystemSetting',array(
                'key' => 'extension_packages',
            ));

            if (empty($setting)) {
                $setting = $modx->newObject('modSystemSetting');
                $setting->set('key','extension_packages');
                $setting->set('namespace','core');
                $setting->set('xtype','textfield');
                $setting->set('area','system');
            }

            $value = $setting->get('value');
            $value = $modx->fromJSON($value);

            if (empty($value)) {
                $value = array();
                $value['modldap'] = array(
                    'path' => '[[++core_path]]components/modldap/model/',
                );
                $value = '['.$modx->toJSON($value).']';
            } else {
                $found = false;
                foreach ($value as $k => $v) {
                    foreach ($v as $kk => $vv) {
                        if ($kk == 'modldap') {
                            $found = true;
                        }
                    }
                }

                if (!$found) {
                    $value[]['modldap'] = array(
                        'path' => '[[++core_path]]components/modldap/model/',
                    );
                }

                $value = $modx->toJSON($value);
            }

            $value = str_replace('\\','',$value);

            $setting->set('value',$value);
            $setting->save();

            break;

        /* remove on uninstall */
        case xPDOTransport::ACTION_UNINSTALL:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('modldap.core_path',null,$modx->getOption('core_path').'components/modldap/').'model/';

            $setting = $modx->getObject('modSystemSetting',array(
                'key' => 'extension_packages',
            ));

            $value = $setting->get('value');
            $value = $modx->fromJSON($value);

            unset($value['modldap']);

            $value = $modx->toJSON($value);

            $setting->set('value',$value);
            $setting->save();

            break;
    }
}

return $success;
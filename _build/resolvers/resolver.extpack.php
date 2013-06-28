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
 * Add activedirectoryx package path to extension_packages setting
 *
 * @package activedirectoryx
 * @subpackage build
 */
$success = true;
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        /* ensure setting is correct on install and upgrade */
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('activedirectoryx.core_path',null,$modx->getOption('core_path').'components/activedirectoryx/').'model/';
            //$modx->addPackage('activedirectoryx',$modelPath);

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
                $value['activedirectoryx'] = array(
                    'path' => '[[++core_path]]components/activedirectoryx/model/',
                );
                $value = '['.$modx->toJSON($value).']';
            } else {
                $found = false;
                foreach ($value as $k => $v) {
                    foreach ($v as $kk => $vv) {
                        if ($kk == 'activedirectoryx') {
                            $found = true;
                        }
                    }
                }

                if (!$found) {
                    $value[]['activedirectoryx'] = array(
                        'path' => '[[++core_path]]components/activedirectoryx/model/',
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
            $modelPath = $modx->getOption('activedirectoryx.core_path',null,$modx->getOption('core_path').'components/activedirectoryx/').'model/';

            $setting = $modx->getObject('modSystemSetting',array(
                'key' => 'extension_packages',
            ));

            $value = $setting->get('value');
            $value = $modx->fromJSON($value);

            unset($value['activedirectoryx']);

            $value = $modx->toJSON($value);

            $setting->set('value',$value);
            $setting->save();

            break;
    }
}

return $success;
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
 * Add modldap package path to extension_packages setting
 *
 * @package modldap
 * @subpackage build
**/
if ($object->xpdo) {
    /** @var modX $modx */
    $modx =& $object->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('modldap.core_path');

            if (empty($modelPath)) {
                $modelPath = '[[++core_path]]components/modldap/model/';
            }

            if ($modx instanceof modX) {
                //$modx->addExtensionPackage('modldap', $modelPath);
                $modx->addExtensionPackage('modldapuser', $modelPath);
            }

            break;
        case xPDOTransport::ACTION_UNINSTALL:
            if ($modx instanceof modX) {
                //$modx->removeExtensionPackage('modldap');
                $modx->removeExtensionPackage('modldapuser', $modelPath);
            }

            break;
    }
}
return true;
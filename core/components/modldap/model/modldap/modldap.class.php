<?php
/**
 * ModLDAP
 *
 * Copyright 2010 by Shaun McCormick <shaun@modx.com>
 * Modified in 2015 by Zaenal Muttaqin <zaenal@lokamaya.com>
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
 * The base class for ModLDAP.
 *
 * @package modldap
 */
class modLDAP {
    
    function __construct(modX &$modx, array $config = array()) {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('modldap.core_path', $config,$this->modx->getOption('core_path') . 'components/modldap/');
        $assetsPath = $this->modx->getOption('modldap.assets_path', $config,$this->modx->getOption('assets_path') . 'components/modldap/');
        $assetsUrl = $this->modx->getOption('modldap.assets_url', $config,$this->modx->getOption('assets_url') . 'components/modldap/');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            //'assetsUrl' => $assetsUrl,
            //'cssUrl' => $assetsUrl . 'css/',
            //'jsUrl' => $assetsUrl . 'js/',
            //'imagesUrl' => $assetsUrl . 'images/',

            'connectorUrl' => $connectorUrl,

            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'pagesPath' => $corePath . 'elements/pages/',
            'eventsPath' => $corePath . 'elements/events/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',
            'hooksPath' => $corePath . 'hooks/',
            //'useCss' => true,
            //'loadJQuery' => true,
        ), $config);

        $this->modx->addPackage('modldap', $this->config['modelPath']);
    }

    /**
     * Initializes ModLDAP into different contexts.
     *
     * @access public
     * @param string $ctx The context to load. Defaults to web.
     */
    public function initialize($ctx = 'web') {
        switch ($ctx) {
            /*
            case 'mgr':
                if (!$this->modx->loadClass('modldap.request.LDAPControllerRequest', $this->config['modelPath'], true, true)) {
                    return 'Could not load controller request handler.';
                }

                $this->request = new LDAPControllerRequest($this);

                return $this->request->handleRequest();
            break;

            case 'connector':
                if (!$this->modx->loadClass('modldap.request.LDAPConnectorRequest', $this->config['modelPath'], true, true)) {
                    return 'Could not load connector request handler.';
                }

                $this->request = new LDAPConnectorRequest($this);

                return $this->request->handle();
            break;
            */

            default:
                $this->modx->lexicon->load('modldap:web');
            break;
        }
    }

    public function loadDriver() {
        $modldapdriver = $this->modx->getService('modldapdriver', 'modLDAPDriver', $this->config['modelPath'] . 'modldap/');

        if (!($modldapdriver instanceof modLDAPDriver)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'[ModLDAP] Could not load modLDAPDriver class from: ' . $this->config['modelPath']);

            return null;
        }

        return $modldapdriver;
    }

    public function loadUserHandling() {
        $modldapuser = $this->modx->getService('modldapuser', 'modLDAPUser', $this->config['modelPath'] . 'modldap/');

        if (!($modldapdriver instanceof modLDAPUser)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'[ModLDAP] Could not load modLDAPUser class from: ' . $this->config['modelPath']);

            return null;
        }

        return $modldapdriver;
    }

    /**
     * Gets a Chunk and caches it; also falls back to file-based templates
     * for easier debugging.
     *
     * @access public
     * @param string $name The name of the Chunk
     * @param array $properties The properties for the Chunk
     * @return string The processed content of the Chunk
     */
    public function getChunk($name, array $properties = array()) {
        $chunk = null;

        if (!isset($this->chunks[$name])) {
            $chunk = $this->modx->getObject('modChunk', array('name' => $name), true);

            if (empty($chunk)) {
                $chunk = $this->_getTplChunk($name);

                if ($chunk == false) {
                    return false;
                }
            }

            $this->chunks[$name] = $chunk->getContent();
        } else {
            $o = $this->chunks[$name];

            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
        }

        $chunk->setCacheable(false);

        return $chunk->process($properties);
    }

    /**
     * Returns a modChunk object from a template file.
     *
     * @access private
     * @param string $name The name of the Chunk. Will parse to name.chunk.tpl
     * @return modChunk/boolean Returns the modChunk object if found, otherwise
     * false.
     */
    private function _getTplChunk($name) {
        $chunk = false;

        $f = $this->config['chunksPath'] . strtolower($name) . '.chunk.tpl';

        if (file_exists($f)) {
            $o = file_get_contents($f);

            $chunk = $this->modx->newObject('modChunk');
            $chunk->set('name',$name);
            $chunk->setContent($o);
        }

        return $chunk;
    }

    public function getGroupsFromInfo($data) {
        if (empty($data['memberof'])) {
            return array();
        }
        
        $groupStrings = $data['memberof'];
        $adGroups = array();

        foreach ($groupStrings as $k => $groupString) {
            if (!is_int($k)) continue;

            $groupData = explode(',', $groupString);

            foreach ($groupData as $groupDataRecord) {
                if (strpos($groupDataRecord, 'CN=') === false && strpos($groupDataRecord, 'cn=') === false) continue;

                $groupDataRecord = str_replace(array('CN=', 'cn='), '', $groupDataRecord);

                if (!empty($groupDataRecord)) {
                    $adGroups[] = $groupDataRecord;
                }
            }
        }

        $adGroups = array_unique($adGroups);

        return $adGroups;
    }
}
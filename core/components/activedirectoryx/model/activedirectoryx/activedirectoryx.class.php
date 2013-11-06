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
 * The base class for ActiveDirectoryX.
 *
 * @package activedirectoryx
 */
class activeDirectoryX {
    
    function __construct(modX &$modx, array $config = array()) {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('activedirectoryx.core_path', $config,$this->modx->getOption('core_path') . 'components/activedirectoryx/');
        $assetsPath = $this->modx->getOption('activedirectoryx.assets_path', $config,$this->modx->getOption('assets_path') . 'components/activedirectoryx/');
        $assetsUrl = $this->modx->getOption('activedirectoryx.assets_url', $config,$this->modx->getOption('assets_url') . 'components/activedirectoryx/');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',

            'connectorUrl' => $connectorUrl,

            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'pagesPath' => $corePath . 'elements/pages/',
            'eventsPath' => $corePath . 'elements/events/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',
            'hooksPath' => $corePath . 'hooks/',
            'useCss' => true,
            'loadJQuery' => true,
        ), $config);

        $this->modx->addPackage('activedirectoryx', $this->config['modelPath']);
    }

    /**
     * Initializes ActiveDirectoryX into different contexts.
     *
     * @access public
     * @param string $ctx The context to load. Defaults to web.
     */
    public function initialize($ctx = 'web') {
        switch ($ctx) {
            case 'mgr':
                if (!$this->modx->loadClass('activedirectoryx.request.ADirControllerRequest', $this->config['modelPath'], true, true)) {
                    return 'Could not load controller request handler.';
                }

                $this->request = new ADirControllerRequest($this);

                return $this->request->handleRequest();
            break;

            case 'connector':
                if (!$this->modx->loadClass('activedirectoryx.request.ADirConnectorRequest', $this->config['modelPath'], true, true)) {
                    return 'Could not load connector request handler.';
                }

                $this->request = new ADirConnectorRequest($this);

                return $this->request->handle();
            break;

            default:
                $this->modx->lexicon->load('activedirectoryx:web');
            break;
        }
    }

    public function loadDriver() {
        $activedirectoryxdriver = $this->modx->getService('activedirectoryxdriver', 'activeDirectoryXDriver', $this->config['modelPath'] . 'activedirectoryx/');

        if (!($activedirectoryxdriver instanceof activeDirectoryXDriver)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'[ActiveDirectoryX] Could not load activeDirectoryXDriver class from: ' . $this->config['modelPath']);

            return $activedirectoryxdriver; // TODO : remove?
        }

        return $activedirectoryxdriver;
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

    /**
     * Sync the User's Profile with the ActiveDirectory data
     *
     * TODO: After Revo 2.0.1, move this to modActiveDirectoryUser. Cant now
     * because class isnt accessible from onauthenticate
     * 
     * @param modUserProfile $profile
     * @param array $data An array of userinfo data
     * @return boolean
     */
    public function syncProfile(modUserProfile &$profile, $data) {
        $fullNameField = $this->modx->getOption(activeDirectoryXDriver::OPT_FULL_NAME_FIELD, null, 'displayname');
        /* map of ActiveDirectory => MODx Profile fields */
        $map = array(
            $fullNameField => 'fullname',
            'mail' => 'email',
            'streetaddress' => 'address',
            'l' => 'city',
            'st' => 'state',
            'co' => 'country',
            'postalcode' => 'zip',
            'mobile' => 'mobilephone',
            'telephonenumber' => 'phone',
            'info' => 'comment',
            'wwwhomepage' => 'website',
        );

        foreach ($data as $k => $v) {
            if (!is_array($v) || !array_key_exists($k,$map)) continue;

            $this->modx->log(xPDO::LOG_LEVEL_DEBUG, '[ActiveDirectoryX] Syncing field "' . $map[$k] . '" to: "' . $v[0] . '"');

            $profile->set($map[$k], $v[0]);
        }
        $id = $profile->get('internalKey');

        if (!empty($id)) {
            $saved = $profile->save();
        }

        //$saved = $user->syncProfile($userInfo);

        if (!$saved) {
            $this->modx->log(modX::LOG_LEVEL_INFO,'[ActiveDirectoryX] User Profile information was unable to be synced.');
        }

        return $saved;
    }
}
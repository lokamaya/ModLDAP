<?php
/**
 * ModLDAP
 *
 * Copyright 2016 by Zaenal Muttaqin <zaenal(#)lokamaya.com>
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
    const MODLDAP_MODLDAP = 'ModLDAP';                                          // ModLDAP
    const MODLDAP_MODLDAPUSER = 'modLDAPUser';                                  // modLDAPUser
    const MODLDAP_MODLDAPDRIVER = 'modLDAPDriver';                              // modLDAPDriver
    
    // Administration
    const MODLDAP_ENABLED = 'modldap.enabled';                                  // LDAP Enabled
    const LOGIN_MANAGER_DISABLE = 'modldap.login_manager_disable';              // Disable LDAP for Manager
    const LOGIN_WEB_DISABLE = 'modldap.login_web_disable';                      // Disable LDAP for Website
    const UPDATE_LDAP = 'modldap.update_ldap';                                  // Enabled Update to LDAP
    
    // Network
    const DOMAIN_CONTROLLERS = 'modldap.domain_controllers';                    // Domain Controllers
    const CONNECTION_TYPE = 'modldap.connection_type';                          // LDAP Connection Type
    const SSL_PORT = 'modldap.ssl_port';                                        // LDAP SSL Port
    const LDAP_OPT_PROTOCOL_VERSION = 'modldap.ldap_opt_protocol_version';      // LDAP Protocol Version
    const LDAP_OPT_REFERRALS = 'modldap.ldap_opt_referrals';                    // Follow LDAP Referrals
    const LDAP_OPT_NETWORK_TIMEOUT = 'modldap.ldap_opt_network_timeout';        // LDAP Network Timeout
    const LDAP_OPT_TIMELIMIT = 'modldap.ldap_opt_timelimit';                    // LDAP Timeout Limit
    const LDAP_OPT_DEBUG = 'modldap.ldap_opt_debug';                            // LDAP Debug
    
    // format 
    const FORMAT_LDAP_BIND = 'modldap.format_ldap_bind';                        // Bind Format
    const FORMAT_LDAP_SEARCH_BASEDN = 'modldap.format_ldap_search_basedn';      // Search Format: BaseDN
    const FORMAT_LDAP_SEARCH_FILTER = 'modldap.format_ldap_search_filter';      // Search Format: Filter
    const FORMAT_LDAP_GROUPS = 'modldap.format_ldap_groups';                    // LDAP Group(s) regex
    
    // AUTOADD LDAP Group
    const AUTOADD_USERGROUPS = 'modldap.autoadd_usergroups';                    // Auto Add MODx UserGroup
    const AUTOADD_USERGROUPS_NAME = 'modldap.autoadd_usergroups_name';          // Default MODx UserGroup
    const AUTOADD_USERGROUPS_ROLE = 'modldap.autoadd_usergroups_role';          // Auto add MODx roles to User Groups
    
    // AUTOADD MODx Group
    const LDAP_GROUP_ADD = 'modldap.ldap_group_add';                            // Auto-Add LDAP Groups to MODx
    const LDAP_GROUP_FIELD = 'modldap.ldap_group_field';                        // LDAP Group Field
    const LDAP_GROUP_ROLE = 'modldap.ldap_group_role';                          // Role for LDAP groups
    
    // MODx => LDAP : fields maps
    const FIELD_FULLNAME = 'modldap.field_fullname';                            // LDAP Field: Fullname
    const FIELD_EMAIL = 'modldap.field_email';                                  // LDAP Field: Email
    const FIELD_PHONE = 'modldap.field_phone';                                  // LDAP Field: Phone
    const FIELD_MOBILEPHONE = 'modldap.field_mobilephone';                      // LDAP Field: Mobilephone
    const FIELD_DOB = 'modldap.field_dob';                                      // LDAP Field: Dob
    const FIELD_GENDER = 'modldap.field_gender';                                // LDAP Field: Gender
    const FIELD_ADDRESS = 'modldap.field_address';                              // LDAP Field: Address
    const FIELD_COUNTRY = 'modldap.field_country';                              // LDAP Field: Country
    const FIELD_CITY = 'modldap.field_city';                                    // LDAP Field: City
    const FIELD_STATE = 'modldap.field_state';                                  // LDAP Field: State
    const FIELD_ZIP = 'modldap.field_zip';                                      // LDAP Field: Zip
    const FIELD_FAX = 'modldap.field_fax';                                      // LDAP Field: Fax
    const FIELD_PHOTO = 'modldap.field_photo';                                  // LDAP Field: Photo
    const FIELD_COMMENT = 'modldap.field_comment';                              // LDAP Field: Comment
    const FIELD_WEBSITE = 'modldap.field_website';                              // LDAP Field: Website
    const FIELD_MEMBEROF = 'modldap.field_memberof';                            // LDAP Field: Group or MemberOf
    
    // Photo path and url
    const PHOTO_PATH = 'modldap.photo_path';                                    // Base Photo Path
    const PHOTO_URL = 'modldap.photo_url';                                      // Base Photo URL
    const PHOTO_IMPORT_SIZE = 'modldap.photo_import_size';                      // Base Photo URL
    const PHOTO_IMPORT_QUALITY = 'modldap.photo_import_quality';                // Base Photo URL

    function __construct(modX &$modx, array $config = array()) {
        $this->modx =& $modx;

        $corePath       = $this->modx->getOption('modldap.core_path', $config,$this->modx->getOption('core_path') . 'components/modldap/');
        $assetsPath     = $this->modx->getOption('modldap.assets_path', $config,$this->modx->getOption('assets_path') . 'components/modldap/');
        $assetsUrl      = $this->modx->getOption('modldap.assets_url', $config,$this->modx->getOption('assets_url') . 'components/modldap/');
        $photoPath      = $this->modx->getOption('modldap.photo_path', $config,$this->modx->getOption('assets_path') . 'components/modldap/');
        $photoUrl       = $this->modx->getOption('modldap.photo_url', $config,$this->modx->getOption('assets_url') . 'components/modldap/');
        $connectorUrl   = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            'corePath'      => $corePath,
            'assetsUrl'     => $assetsUrl,
            'photoPath'     => $photoPath,
            'photoUrl'      => $photoUrl,
            'connectorUrl'  => $connectorUrl,

            'corePath'          => $corePath,
            'modelPath'         => $corePath . 'model/',
            'chunksPath'        => $corePath . 'elements/chunks/',
            'pagesPath'         => $corePath . 'elements/pages/',
            'eventsPath'        => $corePath . 'elements/events/',
            'snippetsPath'      => $corePath . 'elements/snippets/',
            'processorsPath'    => $corePath . 'processors/',
            'hooksPath'         => $corePath . 'hooks/',
        ), $config);
        
        //debug//
        //$this->modx->log(modX::LOG_LEVEL_INFO, '[modLDAP] Conctructing and loading packages...');
        $this->modx->addPackage('modldap', $this->config['modelPath']);
        $this->modx->addPackage('modldapuser', $this->config['modelPath']);
    }
    
    /********************************
     * Plugin Processing           *
    ********************************/
    /**
     * process OnUserNotFound
     *
     * @access public
     * @param string $scriptProperties 
     * @return vary
    **/
    public function processOnUserNotFound() {
        $scriptProperties = $this->modx->event->params;
        $this->modx->event->_output = false;
        
        if (empty($scriptProperties['username'])) return;
        
        $user = $this->modx->newObject('modLDAPUser');
        if ( !($user->Driver->authenticate($scriptProperties['username'], $scriptProperties['password'])) ) {
            $this->modx->event->output(false);
            return;
        }
        
        if ($user->createUserFromLDAP($scriptProperties['username'], $user->Driver->getLdapEntries())) {
            $this->modx->event->output(true);
            $this->modx->event->_output = $user;
            $this->modx->event->stopPropagation();
            return;
        }

        return;
    }
    
    /**
     * process OnManagerAuthentication
     *
     * @access public
     * @param string $scriptProperties 
     * @return vary
    **/
    public function processOnManagerAuthentication() {
        $scriptProperties = $this->modx->event->params;
        $this->modx->event->_output = false;

        if (empty($scriptProperties['user']) || !is_object($scriptProperties['user'])) {
            $this->modx->event->output(false);
            return;
        }

        $classKey = $scriptProperties['user']->get('class_key');

        /* authenticate the user */
        $user = $scriptProperties['user'];

        /* if not a valid modUser, skip */
        if (!is_object($user) || !($user instanceof modUser)) {
            $this->modx->event->output(false);
            return;
        }

        $username = $user->get('username');
        $password = $scriptProperties['password'];

        /* if not a modLDAPUser, skip */
        if ($user->get('class_key') != 'modLDAPUser') {
            $this->modx->event->output(false);
            return;
        } 

        /* double check: if not a modLDAPUser object but valid modUser object, recreate $user */
        if (!($user instanceof modLDAPUser) && $classKey == 'modLDAPUser') {
            $username = $user->get('username');
            $user = $this->modx->newObject('modLDAPUser', array('username'=>$username));
        }

        /* attempt to authenticate */
        if (!($user->Driver->authenticate($username,$password))) {
            $this->modx->event->output(false);
            return;
        }

        /* modLDAPUser: update user profile from LDAP entries */
        $user->updateUserFromLDAP($user->Driver->getLdapEntries());

        $scriptProperties['user'] = $user;
        $this->modx->event->params = $scriptProperties;
        $this->modx->event->output(true);
        $this->modx->event->_output = true;

        return;
    }
    
    /**
     * process OnWebAuthentication
     *
     * @access public
     * @param string $scriptProperties 
     * @return vary
    **/
    public function processOnWebAuthentication() {
        return $this->processOnManagerAuthentication();
    }
    
    
    /********************************
     * Utility                     *
    ********************************/
    /**
     * Output debug to Manager Log
     *
     * @access public
     * @param string $username: valid LDAP username
     * @param string $password: valid LDAP password
     * @return void
    **/
    public function testModLDAP($username, $password, $clearLog=false) {
        if ($clearLog) {
            $log = $this->modx->getOption('modldap.core_path', null, $this->modx->getOption('core_path') . 'cache/logs/error.log');
            if (is_file($log)) unlink($log);
        }
        
        //$this->modx->log(modX::LOG_LEVEL_INFO, "[Test:ModLDAP] Start\n-------------------------------------");
        $user = $this->modx->newObject('modLDAPUser');
        
        /*
        if (!isset($_SESSION['ldap_entries'])) {
            if ( !($user->Driver->authenticate($username, $password)) ) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, 'Can not authenticate username: ' . $username);
                return;
            }
            $ldap_entries = $user->Driver->getLdapEntries();
            $_SESSION['ldap_entries'] = $ldap_entries;
        } else {
            $ldap_entries = $_SESSION['ldap_entries'];
        }
        */
        
        $ldap_entries = $user->Driver->getLdapEntries();
        $user->set_ldap_entries($ldap_entries);
        
        $field_mapping = $user->ldap_field_mapping();
        $field_mapping['dn'] = 'dn';
        
        $text = "  <h3>Results</h3>\n";
        $text .= "  <span>Result from LDAP entries:</span>\n";
        $text .= "  <dl>\n";
        foreach($field_mapping as $field=>$map) {
            $value = '';
            if (empty($map)) {
                $map = "<em>empty</em>";
            } else {
                if ($field=='photo') {
                    $value = "[photo]";
                } else if ($field=='memberof') {
                    $groups = $user->getManyLdapEntryGroup($field, false);
                    $value = implode("<br/>- ", $groups);
                } else {
                    $value = $user->getOneLdapEntry($field);
                }
                $map = "<strong>$map</strong>";
            }
            
            $text .= "    <dt>" . strtoupper($field) . " ($map):</dt>\n";
            $text .= "    <dd>- $value &nbsp;</dd>\n";
        }
        $text .= "  </dl>\n";
        
        //$this->modx->log(modX::LOG_LEVEL_INFO, "END\n-------------------------------------\n\n");
        return $text;
    }
    
    /**
     * Initializes ModLDAP into different contexts.
     *
     * @access public
     * @param string $ctx The context to load. Defaults to web.
    **/
    public function initialize($ctx = 'web') {
        //not implemented
    }

    /**
     * Initializes modLDAPDriver
     *
     * @access public
     * @return object modLDAPDriver
    **/
    public function loadDriver() {
        $modldapdriver = $this->modx->getService('modldapdriver', 'modLDAPDriver', $this->config['modelPath'] . 'modldap/');

        if (!($modldapdriver instanceof modLDAPDriver)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'[ModLDAP] Could not load modLDAPDriver class from: ' . $this->config['modelPath']);

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
    
    /**
     * generateCallTrace function for debugging purpose
     * 
     * @return string formated
     * false.
     */
    public function generateCallTrace() {
        $e = new Exception();
        $trace = explode("\n", $e->getTraceAsString());
        // reverse array to make steps line up chronologically
        $trace = array_reverse($trace);
        array_shift($trace); // remove {main}
        array_pop($trace); // remove call to this method
        $length = count($trace);
        $result = array();
        
        for ($i = 0; $i < $length; $i++) {
            $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
        }
        return "\t" . implode("\n\t", $result);
    }
}
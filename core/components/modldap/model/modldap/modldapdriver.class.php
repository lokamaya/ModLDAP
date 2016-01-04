<?php
/**
 * ModLDAP
 *
 * Copyright 2015 by Zaenal Muttaqin <zaenal@lokamaya.com>
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
**/

class modLDAPDriver {    
    const MODLDAPDRIVER = 'modLDAPDriver';                                      // modLDAPDriver
    const MODLDAP_ENABLED = 'modldap.enabled';                                  // LDAP Enabled

    public $config = array();
    public $modx = null;
    
    /**
    * Connection and bind default variables
    *
    * @var mixed
    * @var mixed
    */
    protected $_conn;
    protected $_bind;
    
    protected $_search_attributes;
    
    protected $_ldapDebugLevel;
    protected $_modxDebugLevel;
    
    protected $ldap_entries;

    /**
    * Default Constructor
    *
    * Tries to bind to the AD domain over LDAP or LDAPS
    *
    * @param modX $modx A reference to the modX object
    * @param array $config Array of options to pass to the constructor
    */
    function __construct(modX $modx, array $config = array()) {   
        $this->modx =& $modx;
        
        if ($this->checkLdapSupport() === false) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[ModLDAP:Driver] No LDAP support for PHP. See: http://www.php.net/ldap');
        }
        
        $this->config = array_merge(array(
        ), $config);
        
        $this->_ldapDebugLevel = (int)$this->getOption(modLDAP::LDAP_OPT_DEBUG, 0);
        $this->_modxDebugLevel = (int)$this->modx->getLogLevel();
        $this->_search_attributes = $this->getAttributsFromFields();
        
        $this->logDebug(modX::LOG_LEVEL_INFO, '[ModLDAP:Driver] modLDAPDriver initialized...');
        
        // DO NOT AUTO CONNECT: Only connect when needed
        // $this->connect();
    }

    /**
    * Default Destructor
    *
    * Closes the LDAP connection
    *
    * @return void
    */
    function __destruct() { 
        $this->close();
    }

    public function is_connected() {
        return $this->_conn;
    }

    public function setOption($k, $v) {
        $this->config[$k] = $v;
    }

    public function getOption($k, $return = '') {
        return $this->modx->getOption($k, $this->config, $return);
    }
    
    /**
    * Connects and Binds to the Domain Controller
    *
    * @return bool
    */
    public function connect() {
        if ($this->is_connected()) return;
        
        $connection_type = $this->getOption(modLDAP::CONNECTION_TYPE, 'NORMAL');
        $connection_port = (int)$this->getOption(modLDAP::SSL_PORT, 636);
        
        //Output LDAP Debuging: 7
        @ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, $this->_ldapDebugLevel);
        
        $dc = $this->getRandomController();
        if ($connection_type = 'SSL') {
            $this->modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP:Driver] Connecting to ldaps://' . $dc . ' using ' . $connection_type . ' connection!');
            $this->_conn = @ldap_connect("ldaps://".$dc, $connection_port);
        } else {
            $this->modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP:Driver] Connecting to ' . $dc . ' using NORMAL connection!');
            $this->_conn = @ldap_connect($dc);
        }
        
        if (!$this->_conn) {
            $this->getLastError('Can not connect to ' . $dc . ' using ' . $connection_type . ' connection!');
            return false;
        }

        // Set some ldap options for talking to AD
        @ldap_set_option($this->_conn, LDAP_OPT_PROTOCOL_VERSION, (int)$this->getOption(modLDAP::LDAP_OPT_PROTOCOL_VERSION, 3));
        @ldap_set_option($this->_conn, LDAP_OPT_REFERRALS, (int)$this->getOption(modLDAP::LDAP_OPT_REFERRALS, 0));
        @ldap_set_option($this->_conn, LDAP_OPT_TIMELIMIT, (int)$this->getOption(modLDAP::LDAP_OPT_NETWORK_TIMEOUT, 10));
        @ldap_set_option($this->_conn, LDAP_OPT_TIMELIMIT, (int)$this->getOption(modLDAP::LDAP_OPT_TIMELIMIT, 10));

        if ($connection_type == 'TLS') {
            $tls = @ldap_start_tls($this->_conn);
            if (!$tls) {
                $this->getLastError('TLS error:');
                return false;
            }
        }
        
        return true;
    }

    /**
    * Closes the LDAP connection
    *
    * @return void
    */
    public function close() {
        if ($this->is_connected()) {
            @ldap_close($this->_conn);
        }
        $this->_conn=null;
    }

    /**
    * Validate a user's login credentials
    *
    * @param string $username A user's LDAP username
    * @param string $password A user's LDAP password
    * @param bool $preventRebind
    *
    * @internal param \optional $bool $prevent_rebind
    * @return bool
    **/
    public function authenticate($username, $password){
        if (!($this->is_connected())) {
            if (!$this->connect()) return false;
        }
        
        if (empty($username) || empty($password)) {
            $this->logDebug(modX::LOG_LEVEL_INFO, '[ModLDAP:Driver] Username or Password is empty!');
            return false;
        }
        
        $this->logDebug(modX::LOG_LEVEL_INFO, '[ModLDAP:Driver] Authenticating username: ' . $username);
        
        // Bind username using desired format
        $remote_key    = $this->parseBindUsername($username, $password);
        
        // Data
        $_username = $this->parseBindUsername($username, $password);
        $this->ldap_entries = array(
            'username'      => $username, 
            'password'      => $password, 
            'remote_key'    => $_username, 
            'result'        => array(),
        );
        
        if ($this->_bind = $this->modldap_bind($_username, $password)) {
            $remote_basedn = $this->parseSearchBaseDN($username); //array
            $remote_filter = $this->parseSearchFilter($username); //array
            
            if (empty($remote_basedn)) {
                $this->logDebug(modX::LOG_LEVEL_INFO, '[ModLDAP:Driver] Can not find valid BaseDN, modldap.format_ldap_search_basedn is empty!');
                return false;
            }
            
            $_result = array();
            foreach ($remote_basedn as $basedn) {
                foreach ($remote_filter as $filter) {
                    $basedn = trim($basedn);
                    $filter = trim($filter);
                    
                    if ($_entries = $this->modldap_search_entries($basedn, $filter, $this->_search_attributes)) {
                        $_result = array_merge($_result, $_entries);
                    }
                }
            }
            
            if (empty($_result)) {
                return false;
            }
            
            $this->ldap_entries['result'] = $_result;
            unset($_result);
            return true;
        }
    }
    
    public function modldap_bind($username, $password) {
        $_bind = @ldap_bind($this->_conn, $username, $password);

        if (!$_bind) {
            $this->getLastError('ldap_bind: can not authenticate ' . $username . ':');
            return false;
        }
        
        return $_bind;
    }
    
    public function modldap_search_entries($basedn, $filter, $attributes) {
        $src = @ldap_search($this->_conn, $basedn, $filter, $attributes);
        
        if ($src) {
            if (ldap_count_entries($this->_conn, $src) > 0) {
                return ldap_get_entries($this->_conn, $src);
            } else {
                $this->getLastError('ldap_count_entries 0 or null:');
            }
        } else {
            $this->getLastError('ldap_search error: ' . " 'ldap_search($basedn, $filter, " . serialize($this->_search_attributes) . ")'.");
        }
        
        return false;
    }

    /**
    * Find information about the users
    * @return array
    */
    public function getLdapEntries(){
        return $this->ldap_entries;
    }
    
    /**
    * Get last error from LDAP
    *
    * This function gets the last message from LDAP
    * set modldap.ldap_opt_debug=7 to get success/error message (see PHP manual)
    *
    * @return string
    */
    private function getLastError($text='') {
        if ($this->_ldapDebugLevel<7) return '';
        
        $this->logDebug(modX::LOG_LEVEL_DEBUG, '[ModLDAP:Driver] ' . $text . ' ' . @ldap_error($this->_conn));
    }
    
    //************************************************************************************************************
    // SERVER FUNCTIONS
    /**
    * Select a random domain controller from your domain controller array
    *
    * @return string
    */
    protected function getRandomController() {
        mt_srand(doubleval(microtime()) * 100000000); // For older PHP versions

        $controllers = explode(',', $this->getOption(modLDAP::DOMAIN_CONTROLLERS, '127.0.0.1'));

        return $controllers[array_rand($controllers)];
    }
    
    /**
    * Find the Base DN of your domain controller
    *
    * @return string
    */
    private function findBaseDn() {
        if ($namingContext = $this->getRootDse(array('defaultnamingcontext'))) {
            return array($namingContext[0]['defaultnamingcontext'][0]);
        }
        
        return array();
    }

    /**
    * Get the RootDSE properties from a domain controller
    *
    * @param array $attributes The attributes you wish to query e.g. defaultnamingcontext
    * @return array
    */
    private function getRootDse(array $attributes = array('*', '+')) {
        if (!$this->_bind){
            return false;
        }

        $sr = @ldap_read($this->_conn, null, 'objectClass=*', $attributes);
        $entries = @ldap_get_entries($this->_conn, $sr);

        return $entries;
    }

    //************************************************************************************************************
    // UTILITY FUNCTIONS (Many of these functions are protected and can only be called from within the class)
    /**
    * Parse username using modldap.format_ldap_bind
    *
    * @return string
    */
    private function parseBindUsername($username, $password) {
        $format = $this->getOption(modLDAP::FORMAT_LDAP_BIND, $username);
        
        return str_replace(array('%username%', '%password%'), array($username, $password), $format);
    }

    /**
    * Get ldap_search BASEDN: modldap.format_ldap_search_basedn
    *
    * @return array
    */
    private function parseSearchBaseDN($username) {
        $format = $this->getOption(modLDAP::FORMAT_LDAP_SEARCH_BASEDN, '');
        
        if (empty($format)) {
            $format = str_replace('%username%', $username, $format);
            $output = preg_split("/[\n\r]+/", $format);
            
            return (array_values(array_filter(array_unique($output))));
        }
        
        return $this->findBaseDn();
    }
    
    /**
    * Parse ldap_search FILTER: modldap.format_ldap_search_filter
    *
    * @return array
    */
    private function parseSearchFilter($username) {
        $format = $this->getOption(modLDAP::FORMAT_LDAP_SEARCH_FILTER, $username);
        $format = str_replace('%username%', $username, $format);
        
        $output = preg_split("/[\n\r]+/", $format);
        return (array_values(array_filter(array_unique($output))));
    }
    
    /**
    * Get ldap_search ATTRIBUTES
    * effective search: only get desired fields
    *
    * @return array
    */
    private function getAttributsFromFields() {
        $fields = array(
            $this->getOption(modLDAP::FIELD_FULLNAME, 'cn'),
            $this->getOption(modLDAP::FIELD_EMAIL, 'mail'),
            $this->getOption(modLDAP::FIELD_PHONE, ''),
            $this->getOption(modLDAP::FIELD_MOBILEPHONE, ''),
            $this->getOption(modLDAP::FIELD_DOB, ''),
            $this->getOption(modLDAP::FIELD_GENDER, ''),
            $this->getOption(modLDAP::FIELD_ADDRESS, ''),
            $this->getOption(modLDAP::FIELD_COUNTRY, ''),
            $this->getOption(modLDAP::FIELD_CITY, ''),
            $this->getOption(modLDAP::FIELD_STATE, ''),
            $this->getOption(modLDAP::FIELD_ZIP, ''),
            $this->getOption(modLDAP::FIELD_FAX, ''),
            $this->getOption(modLDAP::FIELD_PHOTO, ''),
            $this->getOption(modLDAP::FIELD_COMMENT, ''),
            $this->getOption(modLDAP::FIELD_WEBSITE, ''),
            $this->getOption(modLDAP::FIELD_MEMBEROF, 'memberof'),
            );
        
        return (array_values(array_filter(array_unique($fields))));
    }

    /**
    * LogDebug: output log for debuging purpose
    *
    * @param  string $log
    * @return void
    */
    private function logDebug($level, $log) {
        $debug = ($this->_ldapDebugLevel >= $this->_modxDebugLevel) ? TRUE : FALSE;
        if ($debug===FALSE) return;
        
        $this->modx->setLogLevel(modX::LOG_LEVEL_DEBUG);
        $this->modx->log($level, $log);
        $this->modx->setLogLevel($this->_modxDebugLevel);
    }

    /**
    * Detect LDAP support in php
    *
    * @return bool
    */
    protected function checkLdapSupport() {
        return function_exists('ldap_connect');
    }

    /**
    * Converts a binary attribute to a string
    *
    * @param string $bin A binary LDAP attribute
    * @return string
    */
    protected function binary2text($bin) {
        $hex_guid = bin2hex($bin);
        $hex_guid_to_guid_str = '';

        for($k = 1; $k <= 4; ++$k) {
            $hex_guid_to_guid_str .= substr($hex_guid, 8 - 2 * $k, 2);
        }

        $hex_guid_to_guid_str .= '-';

        for($k = 1; $k <= 2; ++$k) {
            $hex_guid_to_guid_str .= substr($hex_guid, 12 - 2 * $k, 2);
        }

        $hex_guid_to_guid_str .= '-';

        for($k = 1; $k <= 2; ++$k) {
            $hex_guid_to_guid_str .= substr($hex_guid, 16 - 2 * $k, 2);
        }

        $hex_guid_to_guid_str .= '-' . substr($hex_guid, 16, 4);
        $hex_guid_to_guid_str .= '-' . substr($hex_guid, 20);

        return strtoupper($hex_guid_to_guid_str);
    }
    
    /**
    * Encode a password for transmission over LDAP
    *
    * @param string $password The password to encode
    * @return string
    */
    protected function encodePassword($password) {
        $password = '"' . $password . '"';
        $encoded = '';

        for ($i=0; $i < strlen($password); $i++) {
            $encoded .= $password[$i] . "\000";
        }

        return $encoded;
    }

    /**
    * Escape strings for the use in LDAP filters
    *
    * @param string $str The string the parse
    * @author Port by Andreas Gohr <andi@splitbrain.org>
    * @return string
    */
    protected function ldapSlashes($str) {
        return preg_replace('/([\x00-\x1F\*\(\)\\\\])/e',
            '"\\\\\".join("",unpack("H2","$1"))',
            $str
        );
    }

    /**
     * Convert 8bit characters e.g. accented characters to UTF8 encoded characters
     * @param $item
     * @param $key
     */
    protected function encode8bit(&$item, $key) {
        $encode = false;

        if (is_string($item)) {
            for ($i=0; $i < strlen($item); $i++) {
                if (ord($item[$i]) >> 7) {
                    $encode = true;
                }
            }
        }

        if ($encode === true && $key != 'password') {
            $item = utf8_encode($item);
        }
    }
}
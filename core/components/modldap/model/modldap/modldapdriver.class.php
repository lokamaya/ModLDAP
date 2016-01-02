<?php
/**
 * ModLDAP
 *
 * Copyright 2015 by Zaenal Muttaqin <zaenal@lokamaya.com>
 *
 * This file is part of ModLDAP, which integrates OpenLDAP
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
**/

class modLDAPDriver {
    /* random constants for future dev */
    const NORMAL_ACCOUNT = 805306368;
    const WORKSTATION_TRUST = 805306369;
    const INTERDOMAIN_TRUST = 805306370;
    const SECURITY_GLOBAL_GROUP = 268435456;
    const DISTRIBUTION_GROUP = 268435457;
    const SECURITY_LOCAL_GROUP = 536870912;
    const DISTRIBUTION_LOCAL_GROUP = 536870913;
    const FOLDER = 'OU';
    const CONTAINER = 'CN';
    //const OPT_ACCOUNT_SUFFIX = 'modldap.account_suffix';
    //const OPT_BASE_DN = 'modldap.base_dn';
    //const OPT_FULL_NAME_FIELD = 'modldap.full_name_field';
    //const OPT_REAL_PRIMARYGROUP = 'modldap.real_primarygroup';
    //const OPT_RECURSIVE_GROUPS = 'modldap.recursive_groups';
    //const OPT_USE_SSL = 'modldap.use_ssl';
    //const OPT_USE_TLS = 'modldap.use_tls';
    
    const OPT_DEBUG_LEVEL = 'modldap.ldap_opt_debug'; // 0=disable, 7=debug
    
    const OPT_CONNECTION_TYPE = 'modldap.ldap_opt_connection_type';  // SSL, TLS, or empty (normal)
    const OPT_LDAP_SSL_PORT = 'modldap.ldap_opt_ssl_port'; //default: 636
    
    const OPT_LDAP_PROTOCOL_VERSION = 'modldap.ldap_opt_protocol_version';  //default: 3
    const OPT_NETWORK_TIMEOUT = 'modldap.ldap_opt_network_timeout';   //default: 10
    const OPT_LDAP_TIMELIMIT = 'modldap.ldap_opt_timelimit';   //default: 10
    const OPT_LDAP_REFERRALS = 'modldap.ldap_opt_referrals';   //default: 0 (disable)

    const FORMAT_LDAP_BIND = 'modldap.format_ldap_bind';
    const FORMAT_LDAP_SEARCH_BASEDN = 'modldap.format_ldap_search_basedn';
    const FORMAT_LDAP_SEARCH_FILTER = 'modldap.format_ldap_search_filter';
    const FORMAT_LDAP_GROUPS = 'modldap.format_ldap_groups';
    
    const LDAP_GROUP_ADD = 'modldap.ldap_group_add';
    const LDAP_GROUP_FIELD = 'modldap.ldap_group_field';
    const LDAP_GROUP_ROLE = 'modldap.ldap_group_role';
    
    const AUTOADD_USERGROUPS = 'modldap.autoadd_usergroups';
    const AUTOADD_USERGROUPS_NAME = 'modldap.autoadd_usergroups_name';
    const AUTOADD_USERGROUPS_ROLE = 'modldap.autoadd_usergroups_role';

    /**
     * @var string Comma-separated list of domain controllers. Specifiy multiple
     * controllers if you would like the class to balance the LDAP queries
     * amongst multiple servers
     */
    const OPT_DOMAIN_CONTROLLERS = 'modldap.domain_controllers';
    
    /**
     * @var string Optional account with higher privileges for searching.
     * This should be set to a domain admin account.
     * :: not used
     */
    const OPT_ADMIN_USERNAME = 'modldap.admin_username';
    const OPT_ADMIN_PASSWORD = 'modldap.admin_password';


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
    protected $_search;
    protected $_basedn;
    protected $_filter;
    protected $_entries;
    protected $_remotekey;

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
        $this->modx->log(modX::LOG_LEVEL_DEBUG, '[ModLDAP:Driver] Initialize modLDAPDriver');
        
        $this->config = array_merge(array(
        ), $config);

        if ($this->checkLdapSupport() === false) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'[ModLDAP:Driver] No LDAP support for PHP. See: http://www.php.net/ldap');
        }

        $this->connect();
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

    public function setOption($k, $v) {
        $this->config[$k] = $v;
    }

    public function getOption($k, $return = '') {
        return $this->modx->getOption($k, $this->config, $return);
    }

    public function is_connected() {
        return $this->_conn;
    }
    
    /**
    * Connects and Binds to the Domain Controller
    *
    * @return bool
    */
    public function connect() {
        $debug_level = $this->getOption(modLDAPDriver::OPT_DEBUG_LEVEL, 0);
        $connection_type = $this->getOption(modLDAPDriver::OPT_CONNECTION_TYPE, 'NORMAL');
        $connection_port = (int)$this->getOption(modLDAPDriver::OPT_LDAP_SSL_PORT, 636);
        
        if ($debug_level > 0) {
            ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, $debug_level);
        }
        
        // Connect to the AD/LDAP server as the username/password
        $dc = $this->getRandomController();
        $this->modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP:Driver] Connecting to ' . $dc . ' using ' . $connection_type . ' connection...');
        
        if ($connection_type = 'SSL') {
            $this->_conn = ldap_connect("ldaps://".$dc, $connection_port);
        } else {
            $this->_conn = ldap_connect($dc);
        }
        
        if (!$this->_conn) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[ModLDAP:Driver] Can not connect to ' . $dc . ' using ' . $connection_type . ' connection!');
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[ModLDAP:Driver][Connect Error] ' . $this->getLastError() . '');
            return false;
        }

        // Set some ldap options for talking to AD
        ldap_set_option($this->_conn, LDAP_OPT_PROTOCOL_VERSION, (int)$this->getOption(modLDAPDriver::OPT_LDAP_PROTOCOL_VERSION, 3));
        ldap_set_option($this->_conn, LDAP_OPT_REFERRALS, (int)$this->getOption(modLDAPDriver::OPT_LDAP_REFERRALS, 0));
        ldap_set_option($this->_conn, LDAP_OPT_TIMELIMIT, (int)$this->getOption(modLDAPDriver::OPT_NETWORK_TIMEOUT, 10));
        ldap_set_option($this->_conn, LDAP_OPT_TIMELIMIT, (int)$this->getOption(modLDAPDriver::OPT_LDAP_TIMELIMIT, 10));

        if ($connection_type == 'TLS') {
            $tls = @ldap_start_tls($this->_conn);
            if (!$tls) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[ModLDAP:Driver] Connect LDAP via TLS error: ' . $dc . '!');
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[ModLDAP:Driver][Connect Error] ' . $this->getLastError() . '');
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
        @ldap_close($this->_conn);
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
    public function authenticate($username, $password, $preventRebind = false){
        if (!($this->is_connected())) {
            $this->modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP:Driver] Connection not available. Authentication fail!');
            return false;
        }
        
        $found = false;
        if (empty($username) || empty($password)) {
            $this->modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP:Driver] Username or Password is empty!' . "$username - $password");
            return $found;
        }

        // Bind as the user
        $remote_key    = $this->parseBindUsername($username, $password);
        $remote_basedn = array_unique($this->parseSearchBaseDN($username));
        $remote_filter = array_unique($this->parseSearchFilter($username));
        
        //print_r($remote_basedn);
        //die();
        
        $this->_bind = @ldap_bind($this->_conn, $remote_key, $password);

        if (!$this->_bind) {
            $this->modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP:Driver] Username or Password not match!' . "$remote_key - $username");
            return $found;
        }
        
        //$this->modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP:Driver] ' . serialize($remote_basedn));
        //$this->modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP:Driver] ' . serialize($remote_filter));
        
        foreach ($remote_basedn as $basedn) {
            foreach ($remote_filter as $filter) {
                $basedn = trim($basedn);
                $filter = trim($filter);
                $this->_search = @ldap_search($this->_conn, $basedn, $filter);
                
                if (($this->_search) && ldap_count_entries($this->_conn, $this->_search) > 0) {
                    $found = true;
                    
                    $this->_remotekey = $remote_key;
                    $this->_basedn = $basedn;
                    $this->_filter = $filter;
                    
                    break;
                } else {
                    $this->modx->log(modX::LOG_LEVEL_INFO, '[ModLDAP:Driver] ' . $this->getLastError() . " for ''ldap_search($basedn, $filter)'' ");
                }
            }
            if ($found) break;
        }
        
        return $found;
    }

    /**
    * Find information about the users
    * @return array
    */
    public function getLdapEntries(){
        if (!$this->_search) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[ModLDAP:Driver] Trying to get LDAP entries before authenticated!');
            return null;
        }
        
        $this->_entries = ldap_get_entries($this->_conn, $this->_search);
        
        $this->_entries['modldap_remotekey']   = $this->_remotekey;
        $this->_entries['modldap_remotedata']  = $this->_basedn . '||' . $this->_filter;
        
        return $this->_entries;
    }
    
    /**
    * Parse username using modldap.format_ldap_bind
    *
    * @return string
    */
    private function parseBindUsername($username, $password) {
        $format = $this->getOption(modLDAPDriver::FORMAT_LDAP_BIND, $username);
        
        return str_replace(array('%username%', '%password%'), array($username, $password), $format);
    }

    /**
    * Parse ldap_search_filter using modldap.format_ldap_search_basedn
    *
    * @return string
    */
    private function parseSearchBaseDN($username) {
        $baseDn = $this->findBaseDn();
        $format = $this->getOption(modLDAPDriver::FORMAT_LDAP_SEARCH_BASEDN, $baseDn);
        $format = str_replace('%username%', $username, $format);
        
        return preg_split("/[\n\r]+/", $format);
    }
    
    /**
    * Parse ldap_search_filter using modldap.format_ldap_search_filter
    *
    * @return string
    */
    private function parseSearchFilter($username) {
        $format = $this->getOption(modLDAPDriver::FORMAT_LDAP_SEARCH_FILTER, $username);
        $format = str_replace('%username%', $username, $format);
        return preg_split("/[\n\r]+/", $format);
    }


    //************************************************************************************************************
    // SERVER FUNCTIONS

    /**
    * Find the Base DN of your domain controller
    *
    * @return string
    */
    private function findBaseDn() {
        $namingContext = $this->getRootDse(array('defaultnamingcontext'));

        return $namingContext[0]['defaultnamingcontext'][0];
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
    * Get last error from Active Directory
    *
    * This function gets the last message from Active Directory
    * This may indeed be a 'Success' message but if you get an unknown error
    * it might be worth calling this function to see what errors were raised
    *
    * @return string
    */
    private function getLastError() {
        return @ldap_error($this->_conn);
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
    * @param string $gid Group ID
    * @param string $usersid User's Object SID
    * @return string
    */
    protected function getPrimaryGroup($gid, $usersid){
        //not implemented
    }

    /**
    * Convert a binary SID to a text SID
    *
    * @param string $binsid A Binary SID
    * @return string
    */
    protected function getTextSID($binsid) {
        //not implemented
    }

    /**
    * Converts a little-endian hex number to one that hexdec() can convert
    *
    * @param string $hex A hex code
    * @return string
    */
    protected function littleEndian($hex) {
        //not implemented
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
    * Converts a binary GUID to a string GUID
    *
    * @param string $binaryGuid The binary GUID attribute to convert
    * @return string
    */
    public function decodeGuid($binaryGuid) {
        //not implemented
    }

    /**
    * Converts a string GUID to a hexdecimal value so it can be queried
    *
    * @param string $strGUID A string representation of a GUID
    * @return string
    */
    protected function strguid2hex($strGUID) {
        //not implemented
    }

    /**
    * Obtain the user's distinguished name based on their userid
    *
    *
    * @param string $username The username
    * @param bool $isGUID Is the username passed a GUID or a samAccountName
    * @return string
    */
    protected function userDn($username, $isGUID=false){
        //not implemented
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
    * Select a random domain controller from your domain controller array
    *
    * @return string
    */
    protected function getRandomController() {
        mt_srand(doubleval(microtime()) * 100000000); // For older PHP versions

        $controllers = explode(',', $this->getOption(modLDAPDriver::OPT_DOMAIN_CONTROLLERS, '127.0.0.1'));

        return $controllers[array_rand($controllers)];
    }

    /**
    * Account control options
    *
    * @param array $options The options to convert to int
    * @return int
    */
    protected function accountControl($options) {
        //not implemented
    }

    /**
    * Take an LDAP query and return the nice names, without all the LDAP prefixes (eg. CN, DN)
    *
    * @param array $groups
    * @return array
    */
    protected function niceNames($groups) {
        //not implemented
    }

    /**
    * Convert a boolean value to a string
    * You should never need to call this yourself
    *
    * @param bool $bool Boolean value
    * @return string
    */
    protected function bool2str($bool) {
        return $bool ? 'TRUE' : 'FALSE';
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
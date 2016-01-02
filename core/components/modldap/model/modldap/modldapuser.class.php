<?php
/**
 * ModLDAP
 *
 * Copyright 2015 by Zaenal Muttaqin <zaenal@lokamaya.com>
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
 * @package modldap
 */
class modLDAPUser extends modUser {
    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key', 'modLDAPUser');
        
        $this->xpdo->log(xPDO::LOG_LEVEL_DEBUG, '[ModLDAP:User] Initializing...');
    }

    /**
    * Sync the User's field: username, etc
    * 
    * @param modUserProfile $scriptProperties
    * @param array $entries An array of entries returned from LDAP
    * @return void 
    **/
    public function syncLDAP(&$scriptProperties, $entries, $notfound=true) {
        $user = null;
        $sync = true; //$this->xpdo->getOption('modldap.add_ldap_user_to_modx', false);
        if ($sync) {
            if ($notfound) {
                $this->set('username', $scriptProperties['username']);
                $this->set('class_key', 'modLDAPUser');
                //$this->set('password', $scriptProperties['password']);
                $this->set('salt', md5(uniqid(rand(),true)));
                $this->set('hash_class', 'hashing.modPBKDF2');
                $this->set('remote_key', $entries['modldap_remotekey']);
                $this->set('remote_data', $entries['modldap_remotedata']);
                $this->set('active', true);
                
                if ($this->save()) {
                    $this->syncProfile($entries, true);
                }
            } else {
                $this->getOne('Profile');
                if (empty($this->Profile)) {
                    $this->syncProfile($entries, true);
                }
            }
        }
        $scriptProperties['user'] = $user;
    }
    
    /**
    * Sync the User's Profile field: fullname, etc
    * 
    * @param modLDAPUser $user
    * @param array $entries An array of entries returned from LDAP
    * @return void
    **/
    private function syncProfile($entries, $noprofile=true) {
        if ((empty($this->Profile)) || ($this->Profile  instanceof modUserProfile)) {
            $this->Profile = $this->xpdo->newObject('modUserProfile');
            $this->Profile->set('internalKey', $this->get('id'));
        }
        
        $map = $this->mapsLdapFields($entries);
        foreach ($entries as $k => $v) {
            if (!is_array($v) || !array_key_exists($k,$map) || empty($v[0])) continue;
            $this->Profile->set($map[$k], $v[0]);
        }
        $this->xpdo->log(xPDO::LOG_LEVEL_INFO, '[ModLDAP:User] Syncing user profile...');
        $this->Profile->save();
    }
    
    /**
    * Sync the User's Group
    * 
    * @param modLDAPUser $user
    * @param array $entries An array of entries returned from LDAP
    * @return void
    **/
    private function syncGroup(modLDAPUser &$user, $entries) {
        $ldap_group_add  = $this->getOption(modldap.ldap_group_add, false, false);
        $ldap_group  = array();
        $ldap_field  = trim($this->getOption(modldap.ldap_group_field, 'memberof'));
        $ldap_roles  = trim($this->getOption(modldap.ldap_group_role, 'Member'));
        $ldap_regex  = $this->getOption(modldap.format_ldap_groups, '', '');
        
        $modx_group_add  = $this->getOption(modldap.autoadd_usergroups, false, false);
        $modx_group_name = $this->getOption(modldap.autoadd_usergroups_name, 'LDAP');
        $modx_group_role = $this->getOption(modldap.autoadd_usergroups_role, 'Member');
        
        if ($ldap_group_add) {
            $ldap_group = $this->parseLdapGroups($entries);
            foreach ($modx_group as $i => $grp) {
                @$this->joinGroup(trim($grp), $modx_roles, $i+10);
            }
        }
        
        if ($modx_group_add) {
            $modx_group   = explode(',', $modx_group_name);
            $modx_roles   = explode(',', $modx_group_role);
            $default_role = (count($modx_roles) < count($modx_group));
            
            foreach ($modx_group as $i => $grp) {
                if ($default_role) {
                    @$this->joinGroup(trim($grp),trim($modx_roles[0]), $i+1);
                } else {
                    @$this->joinGroup(trim($grp),trim($modx_roles[$i]), $i+1);
                }
            }
        }

    }
    
    /**
    * Parse groups from entries
    *
    * @return string
    */
    private function parseLdapGroups($entries) {
        $ldap_field  = trim($this->getOption(modldap.ldap_group_field, 'memberof'));
        $ldap_roles  = trim($this->getOption(modldap.ldap_group_role, 'Member'));
        $ldap_regex  = $this->getOption(modldap.format_ldap_groups, '', '');
        
        $groups_ldap = array();
        
        $_grp = null;
        if (isset($entries[$ldap_field])) {
            foreach($entries[$ldap_field] as $val) {
                if (empty($ldap_regex)) {
                    $groups_ldap[] = trim($val);
                } elseif (@preg_match($ldap_regex, $val, $_grp)) {
                    $groups_ldap[] = trim($_grp[1]);
                }
            }
        }
        
        return $groups_ldap;
    }

    /**
     * Maps LDAP fields to MODx fields using given modldap.maps_fields setting
     * 
     * @param modUserProfile $profile
     * @param array $data An array of userinfo data
     * @return array
     */
    private function mapsLdapFields($data) {
        $maps = array();
        $maps_fields = $this->xpdo->getOption('modldap.maps_fields', null, null);
        
        if (empty($data) || empty($maps_fields)) {
            return $maps;
        }
        
        $fields = preg_split("/[\n\r\s]+/", $maps_fields);
                foreach ($fields as $field) {
            $row = explode("=", $field);
            $k = trim($row[1]);
            $v = trim($row[0]);
            if (!empty($k) && !empty($v)) {
                $maps[$k] = $v;
            }
        }
        
        return $maps;
    }


}
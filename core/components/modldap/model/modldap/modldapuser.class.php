<?php
/**
 * ModLDAP
 *
 * Copyright 2015 by Zaenal Muttaqin <zaenal(#)lokamaya.com>
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
 * @package modldap
 */
class modLDAPUser extends modUser {
    protected $ldap_remote_key;
    protected $ldap_entries;
    protected $ldap_entries_blob;
    protected $ldap_fields_map;
    
    public $Driver;
    
    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        
        $this->set('class_key', 'modLDAPUser');
        $modLDAP = $this->xpdo->getService('modldap', 'modLDAP', $this->xpdo->getOption('modldap.core_path', null, $this->xpdo->getOption('core_path') . 'components/modldap/') . 'model/modldap/');
        
        if ($this->xpdo->getOption(modLDAP::MODLDAP_ENABLED, false)) {
            $this->Driver = $modLDAP->loadDriver();
        }
        
        $this->ldap_fields_map = $this->ldapFieldsOption();
        
        $this->xpdo->log(xPDO::LOG_LEVEL_DEBUG, '[ModLDAP:User] Initialized!');
    }

    /**
    * Sync from LDAP to MODx
    * 
    * @param array $entries
    * @param string $username
    * @param string $password
    * @return void
    **/
    public function createUserFromLDAP($entries) {
        $this->ldap_remote_key = $entries['remote_key'];
        
        // Sometime data is too big, especially if LDAP return image blob.
        // And we dont want to store binnary/string photo from LDAP to MySQL
        // blob/image/binnary moved to $this->ldap_entries_blob
        $this->ldap_entries = $this->cleanupLdapEntries($entries['result']);
        unset($entries); 
        
        //if ($username) { //NEW USER
            $this->set('username', $username);
            $this->set('class_key', 'modLDAPUser');
            //do not add password, so modLDAP will always authenticates through LDAP driver
            //$this->set('password', $password);
            $this->set('salt', md5(uniqid(rand(),true)));
            $this->set('hash_class', 'hashing.modPBKDF2');
            $this->set('remote_key', $this->ldap_remote_key);
            $this->set('remote_data', serialize($this->ldap_entries));
            $this->set('active', true);
            
            if ($this->save()) {
                $this->syncProfileFromLDAP();
                $this->syncGroupFromLDAP();
            }
        //}
        return $this;
    }
    
    /**
    * Sync from LDAP to MODx
    * 
    * @param array $entries
    * @param string $username
    * @param string $password
    * @return void
    **/
    public function updateUserFromLDAP($entries) {
        $this->ldap_remote_key = $entries['remote_key'];
        
        // Sometime data is too big, especially if LDAP return image blob.
        // And we dont want to store binnary/string photo from LDAP to MySQL
        // blob/image/binnary moved to $this->ldap_entries_blob
        $this->ldap_entries = $this->cleanupLdapEntries($entries['result']);
        unset($entries); 
        
        $old_remote_data = unserialize($this->get('remote_data'));
        $new_remote_data = $this->ldap_entries;

        if ($old_remote_data != $new_remote_data) {
            $this->getOne('Profile');
            
            if ($this->syncProfileFromLDAP() !== false) {
                $this->set('remote_key', $this->ldap_remote_key);
                $this->set('remote_data', serialize($this->ldap_entries));
                $this->save();
                
                $this->syncGroupFromLDAP();
            }
        }
        
        return $this;
    }
    
    /**
    * Sync to LDAP from MODx
    * 
    * @param array $entries
    * @param string $username
    * @param string $password
    * @return void
    **/
    public function syncToLDAP($entries, $username=null, $password='') {
        return null; //not implemented
    }
    
    /**
    * Sync the User's Profile field: fullname, email, etc
    * 
    * @return void
    **/
    public function syncProfileFromLDAP() {
        if (empty($this->Profile) || !($this->Profile instanceof modUserProfile)) {
            $this->Profile = $this->xpdo->newObject('modUserProfile');
            $this->Profile->set('internalKey', $this->get('id'));
        }
        
        $host = explode(',',$this->xpdo->getOption(modLDAP::DOMAIN_CONTROLLERS, 'localhost'))[0];
        
        $this->Profile->set('fullname',     $this->getOneLdapEntry('fullname', $this->get('username')));
        $this->Profile->set('email',        $this->getOneLdapEntry('email',    $this->get('username') . '@' . $host));
        $this->Profile->set('phone',        $this->getOneLdapEntry('phone'));
        $this->Profile->set('mobilephone',  $this->getOneLdapEntry('mobilephone'));
        $this->Profile->set('dob',          $this->getOneLdapEntry('dob'));
        $this->Profile->set('gender',       $this->getOneLdapEntry('gender'));
        $this->Profile->set('address',      $this->getOneLdapEntry('address'));
        $this->Profile->set('country',      $this->getOneLdapEntry('country'));
        $this->Profile->set('city',         $this->getOneLdapEntry('city'));
        $this->Profile->set('state',        $this->getOneLdapEntry('state'));
        $this->Profile->set('zip',          $this->getOneLdapEntry('zip'));
        $this->Profile->set('fax',          $this->getOneLdapEntry('fax'));
        $this->Profile->set('comment',      $this->getOneLdapEntry('comment'));
        $this->Profile->set('website',      $this->getOneLdapEntry('website'));
        
        //photo: we handle it differently
        $this->Profile->set('photo',        $this->getOneLdapEntryPhoto('photo'));
        
        return $this->Profile->save();
    }
    
    /**
    * Sync the User's Group
    * 
    * @param modLDAPUser $user
    * @param array $entries An array of entries returned from LDAP
    * @return void
    **/
    public function syncGroupFromLDAP() {
        return;
        $ldap_group_add  = $this->getOption(modLDAP::LDAP_GROUP_ADD, false);
        $ldap_group  = array();
        $ldap_roles  = trim($this->getOption(modLDAP::LDAP_GROUP_ROLE, 'Member'));
        
        
        $modx_group_add  = $this->getOption(modLDAP::AUTOADD_USERGROUPS, false);
        $modx_group_name = $this->getOption(modLDAP::AUTOADD_USERGROUPS_NAME, 'LDAP');
        $modx_group_role = $this->getOption(modLDAP::AUTOADD_USERGROUPS_ROLE, 'Member');
        
        if ($ldap_group_add) {
            $ldap_group = $this->parseLdapGroups($entries);
            foreach ($modx_group as $i => $grp) {
                @$this->joinGroup(trim($grp), $modx_roles, $i+10);
            }
        }
        
        if ($modx_group_add) {
            $modx_group   = explode(',', $modx_group_name);
            $modx_roles   = explode(',', $modx_group_role);
            $_default_role = (count($modx_roles) < count($modx_group));
            
            foreach ($modx_group as $i => $grp) {
                if ($_default_role) {
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
        $ldap_field  = trim($this->getOption(modLDAP::LDAP_GROUP_FIELD, 'memberof'));
        $ldap_regex  = $this->getOption(modLDAP::FORMAT_LDAP_GROUPS, '');
        
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
    * Get LDAP entry: one record;
    * 
    * @param  string $field
    * @param  string $default
    * @return string
    **/
    private function getOneLdapEntry($field, $default='') {
        if (empty($field)) return $default;
        if (!is_array($this->ldap_fields_map) || empty($this->ldap_fields_map) || !isset($this->ldap_fields_map[$field])) return $default;
        
        $cn = $this->ldap_fields_map[$field];
        $ln = count($this->ldap_entries);
        
        $_result = '';
        for ($i=0; $i<$ln; $i++) {
            $_found = isset($this->ldap_entries[$i][$cn]) ? $this->ldap_entries[$i][$cn] : null;
            if ($_found) {
                $_count = isset($_found['count']) ? $_found['count'] : 0;
                if ($_count) {
                    //unset($_found['count']);
                    $_result = $_found[0];
                } else {
                    $_result = $_found;
                }
                
                break;
            }
        }
        
        $_result = (empty($_result)) ? $default : $_result;
        
        return $_result;
    }
    
    /**
    * Get LDAP entry: photo
    * 
    * @param  string $field
    * @return string URL
    **/
    private function getOneLdapEntryPhoto() {
        $output = '';
        $field  = 'photo';
        
        if (!is_array($this->ldap_fields_map) || empty($this->ldap_fields_map) || !isset($this->ldap_fields_map[$field])) return;
        
        $cn = $this->ldap_fields_map[$field];
        $ln = count($this->ldap_entries);
        
        $_found = null;
        $_image = null;
        for ($i=0; $i<$ln; $i++) {
            $_found = isset($this->ldap_entries_blob[$i][$cn]) ? $this->ldap_entries_blob[$i][$cn] : null;
            if ($_found && is_array($_found)) {
                $_image = array_pop($_found);
            } else {
                $_image = $_found;
            }
        }
        
        if ($_image) {
            $assetsPath = $this->modx->getOption(modLDAP::PHOTO_PATH, $config,$this->modx->getOption('assets_path') . 'components/modldap/');
            $assetsUrl  = $this->modx->getOption(modLDAP::PHOTO_URL, $config,$this->modx->getOption('assets_url') . 'components/modldap/');
            $imageName  = $this->get('username') . '-' . $this->get('id') . '.jpg';
            
            if (is_string($_image) === true && ctype_print($_image) === false) {
                
                if ($this->imageCreateFromLdapPhotoString($_image, $assetsPath.$imageName)) {
                    $output = $assetsUrl . $imageName;
                }
            } else if(filter_var($_image, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) === TRUE) {
                if(filter_var($_image, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) === TRUE) {
                    if ($_im = @file_get_contents($_image)) {
                        if ($this->imageCreateFromLdapPhotoString($_im, $assetsPath.$imageName)) {
                            $output = $assetsUrl . $imageName;
                        }
                    } else {
                        $output = $_image;
                    }
                }
            }
        }
        
        return $output;
    }
    
    private function imageCreateFromLdapPhotoString($photo, $path) {
        $this->xpdo->log(xPDO::LOG_LEVEL_DEBUG, '[ModLDAP:User] Importing photo profile...');
        
        $retval = false;
        $size = 300;
        if (!is_dir($path) || !is_writable($path)) {
            $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, '[ModLDAP:User] Can not import LDAP photo, ' . $path . ' is not exist or not writeable.');
            return false;
        }
        
        if ($src = @imagecreatefromstring($photo)) {
            $width  = @imagesx($src);
            $height = @imagesy($src);
            $aspect_ratio = $height/$width;
            if ($width <= $size) {
                $new_w = $width;
                $new_h = $height;
            } else {
                $new_w = $size;
                $new_h = abs($new_w * $aspect_ratio);
            }

            $img = @imagecreatetruecolor($new_w,$new_h); 
            @imagecopyresized($img,$src,0,0,0,0,$new_w,$new_h,$width,$height);
            
            if (@imagejpeg($img, $path, 85)) {
                $retval = true;
            }
            
            @imagedestroy($img);
            @imagedestroy($src);
        }
        
        return $retval;
    }
    
    private function cleanupLdapEntries($entries) {
        $this->ldap_entries_blob = array();
        $this->ldap_entries = $entries;
        $field = 'photo';
        
        if (!is_array($this->ldap_fields_map) || empty($this->ldap_fields_map) || !isset($this->ldap_fields_map[$field])) return;
        
        $cn = $this->ldap_fields_map[$field];
        $ln = count($this->ldap_entries);
        
        $_found = null;
        $im = null;
        for ($i=0; $i<$ln; $i++) {
            $_found = isset($this->ldap_entries[$i][$cn]) ? $this->ldap_entries[$i][$cn] : null;
            if ($_found && is_array($_found)) {
                $this->ldap_entries_blob[$i] = array();
                $this->ldap_entries_blob[$i][$cn] = array();
                foreach($_found as $j=>$im) {
                    if ($j!='count') {
                        $this->ldap_entries[$i][$cn][$j] = '%blob%';
                    }
                    $this->ldap_entries_blob[$i][$cn][$j] = $im;
                }
            }
        }
        unset($entries);
        unset($im);
    }

    private function _cleanupBlob($entries) {
        //$this->ldap_entries      = array();
        $this->ldap_entries_blob = array();
        $_entries = array();
        foreach($entries as $_i=>$_entry) {
            $_blob = array();
            $_text = array();
            if (is_array($_entry)) {
                foreach($_entry as $_j=>$_field) {
                    if (is_array($_field)) {
                        foreach($_field as $_k=>$_row) {
                            if (is_string($_row) === true && ctype_print($_row) === false) {
                                $this->ldap_entries_blob[$_i][$_j][$_k] = $_row;
                                $entries[$_i][$_j][$_k] = '%blob%';
                                unset($_row);
                            }
                        }
                    } else {
                        if (is_string($_field) === true && ctype_print($_field) === false) {
                            $this->ldap_entries_blob[$_i][$_j] = $_field;
                            $entries[$_i][$_j] = '%blob%';
                            unset($_field);
                        }
                    }
                }
            }
        }
        
        return $entries;
    }
    
    /**
     * Maps LDAP fields to MODx fields using given modldap.* setting
     * 
     * @param modUserProfile $profile
     * @param array $data An array of userinfo data
     * @return array
     */
    private function ldapFieldsOption() {
        $fields = array(
            'fullname'       => $this->getOption(modLDAP::FIELD_FULLNAME, 'cn'),
            'email'          => $this->getOption(modLDAP::FIELD_EMAIL, 'mail'),
            'phone'          => $this->getOption(modLDAP::FIELD_PHONE, ''),
            'mobilephone'    => $this->getOption(modLDAP::FIELD_MOBILEPHONE, ''),
            'dob'            => $this->getOption(modLDAP::FIELD_DOB, ''),
            'gender'         => $this->getOption(modLDAP::FIELD_GENDER, ''),
            'address'        => $this->getOption(modLDAP::FIELD_ADDRESS, ''),
            'country'        => $this->getOption(modLDAP::FIELD_COUNTRY, ''),
            'city'           => $this->getOption(modLDAP::FIELD_CITY, ''),
            'state'          => $this->getOption(modLDAP::FIELD_STATE, ''),
            'zip'            => $this->getOption(modLDAP::FIELD_ZIP, ''),
            'fax'            => $this->getOption(modLDAP::FIELD_FAX, ''),
            'photo'          => $this->getOption(modLDAP::FIELD_PHOTO, ''),
            'comment'        => $this->getOption(modLDAP::FIELD_COMMENT, ''),
            'website'        => $this->getOption(modLDAP::FIELD_WEBSITE, ''),
            'memberof'       => $this->getOption(modLDAP::FIELD_MEMBEROF, 'memberof')
            );
        
        return array_filter(array_unique($fields));
    }
}
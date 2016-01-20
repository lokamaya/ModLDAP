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
 * @package modldap
 */
class modLDAPUser extends modUser {
    private $ldap_remotekey;
    private $ldap_username;
    private $ldap_password;
    private $ldap_entries;
    private $fields_mapping;
    
    public $Driver;
    public $_countProfile;
    
    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        
        $this->set('class_key', 'modLDAPUser');
        $modLDAP = $this->xpdo->getService('modldap', 'modLDAP', $this->getOption('modldap.core_path', null, $this->getOption('core_path') . 'components/modldap/') . 'model/modldap/');
        
        if ($this->getOption(modLDAP::MODLDAP_ENABLED, false)) {
            $this->Driver = $modLDAP->loadDriver();
        }
        
        $this->ldap_field_mapping();
    }

    /**
     * Inherited function
     *
     * public function set($k, $v= null, $vType= '');
     * public function setSudo($sudo);
     **** public function save($cacheFlag = false);
     * public function remove(array $ancestors = array());
     * public function loadAttributes($target, $context = '', $reload = false) 
     * public function isAuthenticated($sessionContext= 'web')
     * public function endSession()
     * public function passwordMatches($password, array $options = array())
     * public function activatePassword($key)
     **** public function changePassword($newPassword, $oldPassword, $validateOldPassword = true)
     * public function getSessionContexts()
     * public function addSessionContext($context)
     * public function generateToken($salt)
     * public function getUserToken($ctx = '')
     * public function removeSessionContext($context)
     * public function removeSessionContextVars($context)
     * public function removeSessionContextVars($context)
     * public function removeSessionCookie($context)
     * public function hasSessionContext($context)
     * public function countMessages($read = '')
     * public function getSettings()
     * public function getUserGroupSettings()
     * public function getResourceGroups($ctx = '')
     * public function getUserGroups()
     * public function getPrimaryGroup()
     * public function getUserGroupNames()
     * public function isMember($groups,$matchAll = false)
     * public function joinGroup($groupId,$roleId = null,$rank = null)
     * public function leaveGroup($groupId)
     * public function removeLocks(array $options = array())
     * public function generatePassword($length = 10,array $options = array())
     * public function sendEmail($message,array $options = array())
     * public function getDashboard()
     **** public function getPhoto($width = 128, $height = 128, $default = '')
     * public function getProfilePhoto($width = 128, $height = 128)
     * public function getGravatar($size = 128, $default = 'mm')
    **/
    
    /**
     * Overrides modUser::save
     *
     * {@inheritDoc}
     */
    public function save($cacheFlag = false, $fromLDAP=true) {
        $isNew = $this->isNew();
        return parent :: save($cacheFlag);
    }
    
    /**
     * Overrides modUser::changePassword
     *
     * We can not change LDAP Password
     */
    public function changePassword($newPassword, $oldPassword, $validateOldPassword = true) {
        $changed= false;
        return $changed;
    }
    
    /**
     * Overrides modUser::getPhoto
     *
     * {@inheritDoc}
     */
    public function getPhoto($width = 128, $height = 128, $default = '') {
        $img = $default;

        if ($this->Profile->photo) {
            $img = $this->getProfilePhoto($width, $height);
        } elseif ($this->xpdo->getOption('enable_gravatar')) {
            $img = $this->getGravatar($width);
        }

        return $img;
    }
    
    /**
    * Sync from LDAP to MODx
    * 
    * @param array $entries
    * @param string $username
    * @param string $password
    * @return void
    **/
    public function createUserFromLDAP($username, $entries) {
        $this->set_ldap_entries($entries);
        unset($entries);
        
        $this->set('username', $username);
        $this->set('class_key', 'modLDAPUser');
        //Do not store password on MODX, so modLDAPUser will always authenticates through LDAP server
        //$this->set('password', $password);
        $this->set('salt', md5(uniqid(rand(),true)));
        $this->set('hash_class', 'hashing.modPBKDF2');
        //remote data must be clean up
        $remote_data = $this->cleanup_ldap_entries();
        $this->set('remote_data', $remote_data);
        $this->set('remote_key', $this->ldap_remotekey);
        $this->set('active', true);
        
        if ($this->save()) {
            $this->syncProfileFromLDAP();
            
            //Insert to Groups
            $this->autoAddGroupToUserLDAP();
            $this->autoSyncGroupFromUserLDAP();
            
            return true;
        }
        return false;
    }
    
    /**
    * Sync from LDAP to MODx
    * 
    * @param array $entries
    * @return void
    **/
    public function updateUserFromLDAP($entries) {        
        $this->set_ldap_entries($entries);
        unset($entries);
        
        $old_remote_data = unserialize($this->get('remote_data'));
        $new_remote_data = $this->ldap_entries;

        if ($old_remote_data != $new_remote_data) {
            $this->getOne('Profile');
            
            if ($this->syncProfileFromLDAP() !== false) {
                $this->set('remote_key', $this->ldap_remotekey);
                //remote data must be clean up
                $remote_data = $this->cleanup_ldap_entries();
                $this->set('remote_data', $remote_data);
                $this->save();
                
                //Update user group
                $this->autoAddGroupToUserLDAP();
                $this->autoSyncGroupFromUserLDAP();
            }
        }
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
        
        $host = explode(',',$this->getOption(modLDAP::DOMAIN_CONTROLLERS, 'localhost'))[0];
        
        $this->_countProfile = 0;
        
        $this->_setLDAPProfile('fullname',     $this->getOneLdapEntry('fullname'), $this->get('username'));
        $this->_setLDAPProfile('email',        $this->getOneLdapEntry('email'),$this->get('username') . '@' . $host);
        $this->_setLDAPProfile('phone',        $this->getOneLdapEntry('phone'));
        $this->_setLDAPProfile('mobilephone',  $this->getOneLdapEntry('mobilephone'));
        $this->_setLDAPProfile('dob',          $this->getOneLdapEntry('dob'));
        $this->_setLDAPProfile('gender',       $this->getOneLdapEntry('gender'));
        $this->_setLDAPProfile('address',      $this->getOneLdapEntry('address'));
        $this->_setLDAPProfile('country',      $this->getOneLdapEntry('country'));
        $this->_setLDAPProfile('city',         $this->getOneLdapEntry('city'));
        $this->_setLDAPProfile('state',        $this->getOneLdapEntry('state'));
        $this->_setLDAPProfile('zip',          $this->getOneLdapEntry('zip'));
        $this->_setLDAPProfile('fax',          $this->getOneLdapEntry('fax'));
        $this->_setLDAPProfile('comment',      $this->getOneLdapEntry('comment'));
        $this->_setLDAPProfile('website',      $this->getOneLdapEntry('website'));
        $this->_setLDAPProfile('photo',        $this->getOneLdapEntryPhoto('photo')); //photo: we handle it differently: getOneLdapEntryPhoto()
        
        if ($this->_countProfile > 0) {
            return $this->Profile->save();
        }
        
        return true;
    }
    
    protected function _setLDAPProfile($fieldname, $value, $default="") {
        if (empty($value)) $value = $default;
        if (empty($value)) return;
        
        $this->_countProfile++;
        $this->Profile->set($fieldname, $value);
    }
    
    /**
    * Auto-Add MODX User's Group
    * 
    * @return void
    **/
    public function autoAddGroupToUserLDAP() {
        $modx_group_add  = $this->getOption(modLDAP::AUTOADD_USERGROUPS, false, false);
        $modx_group_name = $this->getOption(modLDAP::AUTOADD_USERGROUPS_NAME, null, '');
        $modx_group_role = $this->getOption(modLDAP::AUTOADD_USERGROUPS_ROLE, null, '');
        
        if (!$modx_group_add) return;
        
        $error = false;
        if (empty($modx_group_name)) {
            $error = true;
            $this->xpdo->log(xPDO::LOG_LEVEL_INFO, "[modLDAPUser] " . modLDAP::AUTOADD_USERGROUPS_NAME . " is empty!");
        }
        if (empty($modx_group_role)) {
            $error = true;
            $this->xpdo->log(xPDO::LOG_LEVEL_INFO, "[modLDAPUser] " . modLDAP::AUTOADD_USERGROUPS_ROLE . " is empty!");
        }
        
        if ($error) return false;
        
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
        
        return;
    }
    
    /**
    * Sync LDAP User's Group (not implemented)
    * 
    * @return void
    **/
    public function autoSyncGroupFromUserLDAP() {
        return true; //not implemented
        $ldap_group_add  = $this->getOption(modLDAP::LDAP_GROUP_ADD, false, false);
        $ldap_group  = array();
        $ldap_roles  = trim($this->getOption(modLDAP::LDAP_GROUP_ROLE, null, 'Member'));
        
        if (!$ldap_group_add) return true;
        
        $ldap_group = $this->getManyLdapEntryGroup($entries);
        foreach ($modx_group as $i => $grp) {
            @$this->joinGroup(trim($grp), $modx_roles, $i+10);
        }
    }
    
    /**
     * Maps LDAP fields to MODx fields using given modldap.* setting
     * 
     * @param array $entries
     * @return array
     */
    public function set_ldap_entries($entries) {
        $this->ldap_entries    = $entries['result'];
        $this->ldap_remotekey  = $entries['remotekey'];
        $this->ldap_username   = $entries['username'];
        $this->ldap_password   = $entries['password'];
    }
    
    /**
     * Maps LDAP fields to MODx fields using given modldap.* setting
     * 
     * @return array
     */
    public function ldap_field_mapping() {
        $this->fields_mapping = array(
            'fullname'       => $this->getOption(modLDAP::FIELD_FULLNAME, null, 'cn'),
            'email'          => $this->getOption(modLDAP::FIELD_EMAIL, null, 'mail'),
            'phone'          => $this->getOption(modLDAP::FIELD_PHONE, null, ''),
            'mobilephone'    => $this->getOption(modLDAP::FIELD_MOBILEPHONE, null, ''),
            'dob'            => $this->getOption(modLDAP::FIELD_DOB, null, ''),
            'gender'         => $this->getOption(modLDAP::FIELD_GENDER, null, ''),
            'address'        => $this->getOption(modLDAP::FIELD_ADDRESS, null, ''),
            'country'        => $this->getOption(modLDAP::FIELD_COUNTRY, null, ''),
            'city'           => $this->getOption(modLDAP::FIELD_CITY, null, ''),
            'state'          => $this->getOption(modLDAP::FIELD_STATE, null, ''),
            'zip'            => $this->getOption(modLDAP::FIELD_ZIP, null, ''),
            'fax'            => $this->getOption(modLDAP::FIELD_FAX, null, ''),
            'photo'          => $this->getOption(modLDAP::FIELD_PHOTO, null, ''),
            'comment'        => $this->getOption(modLDAP::FIELD_COMMENT, null, ''),
            'website'        => $this->getOption(modLDAP::FIELD_WEBSITE, null, ''),
            'memberof'       => $this->getOption(modLDAP::FIELD_MEMBEROF, null, 'memberof'),
            );
        
        return $this->fields_mapping;
    }
    
    /**
    * Remove LDAP entry;
    * 
    * @param  string $field
    * @param  string $replace
    * @return string
    **/
    public function cleanup_ldap_entries($field="photo", $replace='%blob%') {
        if (empty($field)) return $this->ldap_entries;
        
        // Get entries and remove "count" key
        $entries = $this->ldap_entries;
        
        // Get mapping field
        $cn = $field;
        $maps = $this->fields_mapping;
        if (array_key_exists($field,$maps)) {
            $cn = $this->fields_mapping[$field];
        }
        
        foreach ($entries as $i=>$entry) {
            if (!is_numeric($i)) continue;
            
            if (array_key_exists($cn,$entry)) {
                $row = $entry[$cn];
                if (is_array($row) && array_key_exists('count',$row) && $row['count'] > 0) {
                    foreach ($row as $k=>$rec) {
                        if (!is_numeric($i)) continue;
                        $entries[$i][$cn][$k] = $replace;
                    }
                } else {
                    $entries[$i][$cn] = $replace;
                }
            }
        }
        
        return $entries;
    }    
    
    /**
    * Get LDAP entry: array record;
    * 
    * @param  string $field
    * @return string
    **/
    public function getManyLdapEntry($field) {
        if (empty($field)) return array();
        
        // Get entries ...
        $entries = $this->ldap_entries;
        if (!is_array($entries)) return array();
        if (empty($entries)) return array();
        
        // Get mapping field
        $cn = $field;
        $maps = $this->fields_mapping;
        if (array_key_exists($field,$maps)) {
            $cn = $this->fields_mapping[$field];
        }
        
        if (empty($cn)) return array();
        
        // Get the results
        $_result = array();
        foreach ($entries as $i=>$entry) {
            if (!is_numeric($i)) continue;
            
            if (array_key_exists($cn,$entry)) {
                $row = $entry[$cn];
                if (is_array($row) && array_key_exists('count',$row) && $row['count'] > 0) {
                    unset($row['count']);
                    foreach ($row as $rec) {
                        $_result[] = $rec;
                    }
                } else if (is_string($row)) {
                    $_result[] = $row;
                }
            } else {
                $this->xpdo->log(xPDO::LOG_LEVEL_INFO, " - getManyLdapEntry($cn) unknwon field!" );
            }
        }
        
        return $_result;
    }
    
    /**
    * Parse groups from entries
    *
    * @return string
    */
    public function getManyLdapEntryGroup($field='memberof', $return_parsed=true) {
        $_result = $this->getManyLdapEntry($field);
        if (empty($_result)) return array();
        
        $ldap_regex  = $this->getOption(modLDAP::FORMAT_LDAP_GROUPS, '', '');
        $_parsed=null;
        if ($return_parsed && !empty($ldap_regex)) {
            foreach($_result as $i=>$group) {
                if (@preg_match($ldap_regex, $group, $_parsed)) {
                    $_result[$i] = trim($_parsed[1]);
                }
            }
        }
        
        return $_result;
    }
    
    /**
    * Get LDAP entry: one record;
    * 
    * @param  string $field
    * @param  int $index
    * @return string
    **/
    public function getOneLdapEntry($field, $index=0) {
        $_result = $this->getManyLdapEntry($field);
        if (!empty($_result)) return $_result[$index];
        
        return '';
    }
    
    /**
    * Get LDAP entry: photo
    * 
    * @param  string $field
    * @return string URL
    **/
    public function getOneLdapEntryPhoto($field='photo') {
        $assetsPath = $this->getOption(modLDAP::PHOTO_PATH, null, $this->getOption('assets_path') . 'components/modldap/');
        $assetsUrl  = $this->getOption(modLDAP::PHOTO_URL, null, $this->getOption('assets_url') . 'components/modldap/');

        $username = $this->get('username');
        if (empty($username)) {
            $username = $this->getOneLdapEntry('fullname');
        }
        $username = preg_replace(array('/[^\s\w]/', '/\s+/'), array('_', ''), $username);
        $imageName  = str_pad($this->get('id'), 8, '0', STR_PAD_LEFT) . '-' . $username . '.jpg';

        $_result = $this->getManyLdapEntry($field);
        $imageURL = null;
        foreach ($_result as $i=>$_image) {
            if (is_string($_image) === true && ctype_print($_image) === false) {
                if ($this->imageCreateFromLdapPhotoString($_image, $assetsPath, $imageName)) {
                    $imageURL = $assetsUrl . $imageName;
                }
            } 
            
            if(empty($imageURL) && filter_var($_image, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) === TRUE) {
                if(filter_var($_image, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED) === TRUE) {
                    if ($_im = @file_get_contents($_image)) {
                        if ($this->imageCreateFromLdapPhotoString($_im, $assetsPath, $imageName)) {
                            $imageURL = $assetsUrl . $imageName;
                        }
                    } else {
                        $imageURL = $_image;
                    }
                }
            }
            
            if (!empty($imageURL)) break;
        }
        
        if (!empty($imageURL)) return $imageURL;
        
        return '';
    }
    
    private function imageCreateFromLdapPhotoString($photo, $path, $imageName) {
        $retval = false;
        $import_size = (int)$this->getOption(modLDAP::PHOTO_IMPORT_SIZE, 300, 300);
        $import_qual = (int)$this->getOption(modLDAP::PHOTO_IMPORT_QUALITY, 75, 75);
        
        if (!is_dir($path) || !is_writable($path)) {
            $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, '[ModLDAP:User] Can not import LDAP photo, ' . $path . ' is not exist or not writeable.');
            return false;
        }
        
        if ($src = imagecreatefromstring($photo)) {
            $width  = imagesx($src);
            $height = imagesy($src);
            $aspect_ratio = $height/$width;
            if ($width <= $import_size) {
                $new_w = $width;
                $new_h = $height;
            } else {
                $new_w = $import_size;
                $new_h = abs($new_w * $aspect_ratio);
            }

            if ($img = imagecreatetruecolor($new_w,$new_h)) {
                if (imagecopyresized($img,$src,0,0,0,0,$new_w,$new_h,$width,$height)) {
                    if (is_file($path.$imageName)) unlink($path.$imageName);
                    if (imagejpeg($img, $path.$imageName, $import_qual)) {
                        $retval = true;
                    }
                }
                imagedestroy($img);
            }
            imagedestroy($src);
        }
        
        return $retval;
    }
}
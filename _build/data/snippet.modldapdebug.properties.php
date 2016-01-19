<?php
/**
 * Default properties for the ModLDAPDebug Snippet
**/
$properties = array(
    array(
        'name' => 'username',
        'desc' => 'prop_modldap.ldap_username',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    ),
    array(
        'name' => 'password',
        'desc' => 'prop_modldap.ldap_password',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    ),
    array(
        'name' => '_object',
        'desc' => 'prop_modldap.distinguish_object',
        'type' => 'textfield',
        'options' => '',
        'value' => 'ModLDAP Snippet',
    ),
);

return $properties;
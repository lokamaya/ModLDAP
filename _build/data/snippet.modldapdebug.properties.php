<?php
/**
 * Default properties for the ModLDAPDebug Snippet
**/
$properties = array(
    array(
        'name' => 'username',
        'desc' => 'Client: LDAP Username',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    ),
    array(
        'name' => 'password',
        'desc' => 'Client: LDAP Password',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    ),
    array(
        'name' => '_object',
        'desc' => 'Distinguish scriptProperties',
        'type' => 'textfield',
        'options' => '',
        'value' => 'ModLDAP Snippet',
    ),
);

return $properties;
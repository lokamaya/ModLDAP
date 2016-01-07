ModLDAP
=======

This is an LDAP integration for MODX Revolution, branched from ActiveDirectoryRedux.

Installation
-----------------------------------------------------
* Simply install via Package Management in MODX Revolution Manager page.
* After installing this package, go to System Setting > ModLDAP, change some setting there.

By default, ModLDAP has been disabled. So you have to edit some System Setting first...

Debugging your LDAP
-----------------------------------------------------
There are 2 step for debugging your LDAP setting:

1. Test your LDAP configuration using plain PHP file provided in assets/components/modldap/
  - View and Edit **_debug.php** file
  - Make sure the connection successful and you get LDAP entries
  - Go to System Setting > ModLDAP, and change some there that reflect your LDAP configuration

2. Test your ModLDAP setting from MODX using **ModLDAPDebug** Snippet
  - Create a new resource
  - Add this below code into it
```
  [[ModLDAPDebug?
      &username=`MyLdapUsername`
      &password=`MyLdapPassword`
  ]]
```

Changelog
-----------------------------------------------------
ModLDAP 2.4.1-alpha
- Developed and tested on MODX Revo 2.4.2
- Refactoring modLDAP base class
- Refactoring LDAP Driver for connection and authentication
- Refactoring modLDAPUser (extends modUSER)
- Plugin has been modified
- A lot of modification to ModLDAP System Setting
- [NEW] Snippet for debuging: ModLDAPDebug
- [NEW] Import user photo from LDAP 
- [NEW] Add LDAP User to certain group and role
- [NOT IMPLEMENTED] Add LDAP Group to MODX
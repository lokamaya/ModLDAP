ModLDAP
=======

This is an LDAP integration for MODX Revolution, branched from ActiveDirectoryRedux (was modActiveDirectory). 

LDAP is short for *Lightweight Directory Access Protocol* and was developed at the University of Michigan around 1993. There are a number of LDAP-enabled servers around, the most common of which is Microsoft’s ActiveDirectory; there’s an open source choice as well, known as OpenLDAP. ModLDAP especially designed for OpenLDAP.

**In short**: ModLDAP is a new extended modUser (modLDAPUser) with ability to authenticate MODX user against LDAP server.


Requirements
-----------------------------------------------------
* MODX 2.4.x++ (tested). But also should work on MODX version 2.2.2++ or 2.3.x++ (need a feedback)
* PHP 5.x++ with LDAP module enabled. For PHP 5.6 or newer you will need to compile PHP with OpenLDAP 2.4 or newer (for more information see http://php.net/manual/en/ldap.setup.php).
* Any LDAP server to work with, including OpenLDAP and Microsoft Active Directory.


Installation
-----------------------------------------------------
* Simply install via Package Management in MODX Revolution Manager page.
* After installing this package, go to System Setting > ModLDAP, change some setting there.

By default, ModLDAP has been disabled. So you have to edit some System Setting first...


Debugging your LDAP
-----------------------------------------------------
There are 2 step for debugging your LDAP setting:

1. Test your LDAP configuration using plain PHP file provided in **assets/components/modldap/_debug.php**
  - Edit some setting: SECURITY and LDAP CONFIGURATION
  - Make sure you edit $_securityVAL = "secretword"; and change it.
  - Access that file using your browser: path/to/file/_debug.php?debug=YourSecretWords
  - Make sure the connection successful and you get LDAP entries
  - Go to System Setting > ModLDAP, and change some setting there that reflect your LDAP configuration

2. Test your ModLDAP setting from MODX using **ModLDAPDebug** Snippet
  - Create a new resource
  - Add below code into it
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
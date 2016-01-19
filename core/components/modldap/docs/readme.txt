--------------------
ModLDAP
--------------------
Version: 2.4.1 beta
Since: December 31th, 2015
Author: Zaenal Muttaqin <zaenal(#)lokamaya.com>
Branched from: ActiveDirectoryRedux 2.4.0 beta1
--------------------


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
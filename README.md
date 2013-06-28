## ActiveDirectoryX

This is an ActiveDirectory integration for MODX Revolution.

## Installation

Simply install via Package Management in MODX Revolution.

* You may need to make the manager/controllers/security/login.php file writable by PHP, if it is not already. ActiveDirectoryX patches a bug in that file that is in Revo 2.0.0-pl.

From there, you'll need to setup some settings:

* activedirectoryx.account_suffix : The account suffix for your domain. Usually in @forest.domain format.
* activedirectoryx.domain_controllers : A comma-separated list of domain controllers. Specifiy multiple controllers if you would like the class to balance the LDAP queries.

## ActiveDirectory Group Synchronization

ActiveDirectoryX will automatically grab all the ActiveDirectory groups a user belongs to, and then search for any MODx UserGroups with matching names. If found, the user will be added to those groups.

If you'd like to disable this, set the activedirectoryx.autoadd_adgroups System Setting to 0.

ActiveDirectoryX also allows you to specify a comma-separated list of MODx UserGroup names to automatically add the User to. This can be set in the activedirectoryx.autoadd_usergroups setting.

Make sure you give the User Groups the User will auto-join access to the manager (through Access Controls), should you want your ActiveDirectory users to have mgr access.

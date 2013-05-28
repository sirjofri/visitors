# Visitors
a small visitor counter for websites.

although the system is published this readme isn't perfect.

## 1. Installation/Initialization
* to install/initialize the system you easily put all the files to one folder on your webserver reachable by everyone.
* next you open the file "dbconnect.inc" and fill in your data.
* then you open the file "vconfig.inc" and set there your configuration. check that the $save value is set to "false"! See the next section for more information about the configuration.
* next you execute "index.php?site=init" from your browser. e. g. if your folder is in /visitors/ you can reach it with "http://.../visitors/index.php?site=init".
* if everything is done you should save your system: set the $save value in "vconfig.inc" to "true". now nobody can re-initialize and destroy your system.

## 2. Configuration
this section isn't ready yet :-(

## 3. Usage
this section isn't ready yet :-(

you should take a look at "visit.php". it is a sample file that shows how you can implement it.

## 4. Remove
Easily delete the files you inserted into the folder during the first initialization step. then you can (you needn't) delete the mysql-tables. i don't want to write an uninstallation script.

## 5. Bugs
There are no bugs known yet. Ok, there are no testers yet, so ...

Please report any bugs you see.

## 6. Future
There are no concrete features planned. i want to make a help menu in the view to increase the user-friendliness. the initialization progress may change (set up more users, create a small user-mysql-table, create a user-system, ...).
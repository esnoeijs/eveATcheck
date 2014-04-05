eveATcheck
==========

eveATcheck is a web application to manage fleet setups and fits for the eve online video game. The express purpose is to be able to check these setups and fits against rules published for tournaments.

Given the meta-game of tournament in eve online this application was made from the onset to be stand-alone. Users are expected to host their own version. This way you don't have to trust me not to steal your brilliant setups.

features
========

- creating/viewing/modifying setups & fits.
- Verifying fits against configured rules.
- user authentication. (setups&fits are shared with everyone)

installation
==========
* clone this repo into a web directory
* create a database and user in mysql, and run install/install.sql to create the application specific tables
* download the latest CCP static dump for eve online. http://wiki.eve-id.net/CCP_Static_Data_Dump#Conversions
* copy config/config.dist.php to config.php and modify the configuration settings.

and your done and your AT team can start creating users and using the application.

updating
==========

Run git pull from time to time. Eventually patch files should be made for mysql changes, but that won't happen until
the application is feature complete. Until that time you will have to manually apply changes as they appear in install.sql

When CCP releases a new static dump the old one should be removed and the new one imported.

Modifying
==========

Rules
------

Rules change, maybe you want special rules, whatever.
rules/ will house the .xml rule files which will specify the rules for a given tournament, the active tournament can be configured in config/config.php

The restriction keywords refer to rule classes housed in app/lib/rulechecker/rules/. These classes should be fairly self explanatory.


Authentification
----------------

Currently there is only a database authentification class. But the application is made with the idea that more types of authentication could be added.
This code can be found in app/lib/user/ and app/lib/user/auth.

Contributing
-------------

Feel free to contribute. Just send me an evemail or something, or just make a push request.

Contact
=======
You can contact me ingame as [roigon](https://gate.eveonline.com/Profile/roigon)


License
=======

At some point I'll decide on a license, for now I rather focus on getting this thing feature complete and production ready.
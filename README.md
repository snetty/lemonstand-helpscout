Lemonstand Sidebar App for Helpscout
====================
This provides the server component for your Lemonstand v1 - Helpscout Integration.

Pull requests welcome.

Dependencies

* Composer require "helpscout/apps": "dev-master"

#Install

1. git clone to /modules/helpscout
2. Create a Custom App in Helpscout (https://secure.helpscout.net/apps/custom/) with Content Type "Dynamic Content"
3. Add your helpscout secret key in /modules/classes/helpscout_modules.php
4. Set Callback Url to https://yourdomain.com/helpscout_callback
5. Enjoy
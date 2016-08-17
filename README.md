Molotok - php auction script
----------------------------

Open source php/mysql fully featured auction script. Perfect for those who want to start their own auction site. The version we offer is meant for up to 20,000 users per day and can store up to 10,000,000 items.

# Installation

### Step 1. File copying and permissions setting
Download the file archive on this page and un-zip it to the root directory of the site, another way is to fork a repository from the Github by executing the following command:
```sh
git clone https://github.com/molotoksoftware/auction.git .
```
Then you need to set permissions on data entry in following directories:
```sh
/backend/runtime
/backend/www/assets
/console/runtime
/frontend/runtime
/frontend/www/assets
/frontend/www/i2
/frontend/www/tmp
/frontend/www/images/users
/frontend/www/images/admins
/frontend/www/images/news
```

### Step 2. Server setting

It should be configured in a way that the root directory would denote `/frontend/www`. Such approach would provide additional protection for the data placed on the same level with `/frontend`. In such a manner, the files placed inside the `/frontend/www` directory, would become accessible while typing main domain name, e.g. `http://youauction.com`.

An example of Apache configuration file
```sh
<VirtualHost 127.0.0.1:80>
        ServerAdmin webmaster@localhost
        ServerName demo.molotoksoftware.com
        DocumentRoot /var/www/demoMolotok/frontend/www
        <Directory />
                Options FollowSymLinks
                AllowOverride All
        </Directory>
        <Directory /var/www/demoMolotok/frontend/www/>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
        </Directory>
        ErrorLog ${APACHE_LOG_DIR}/error.log
        # Possible values include: debug, info, notice, warn, error, crit,
        # alert, emerg.
        LogLevel warn
        CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```
It is recommended to create a separate subdomain (e.g. `admin.youauction.com`) for the administrator’s part of the trading platform. In a similar way with the example above, it is necessary to spell out the route to the folder in an Apache config file (e.g. `/var/www/demoMolotok/backend/www`)

You need mod_rewrite Apache for application operation, you can activate it with the following command:
```sh
a2enmod rewrite
```

### Step 3. Database
Create a new database and import auction.sql (SQL Dump) SQL-file in it. This file is stored in the `/common/data/` directory. 

### Step 4. env.php config file
On this stage you need to configure the component for database connection in `/common/config/env.php`. settings file. Change the parameter value in `mysql:host`, `dbname`, `username` and `password` fields.
```sh
'db'  => [
            'connectionString'      => 'mysql:host=localhost;dbname=dbname',
            'emulatePrepare'        => true,
            'username'              => 'root',
            'password'              => '',
            'charset'               => 'utf8',
            'enableProfiling'       => true,
            'enableParamLogging'    => true,
            'tablePrefix'           => '',
            'schemaCachingDuration' => 0,
        ],
```
### Step 5. Parameter setting
Go to administrator’s part `admin.youauction.com` -> `Settings` section, and specify basic parameters. Access data to the administrator’s part:

 - Username: admin
 - Password: 123456


License
----


Molotok is released completely free of charge under the terms of the GNU General Public License (GPL v.3)

Documentation
----
http://molotoksoftware.com/en/documentation/part/main
   
   [![Yii](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)
Synapp V1
=========
####*A crowdsourcing web platform for exploring and developing creative skills*

 Copyright (C) 2012 Gael Abadin  
 License: [MIT Expat][1]
<br/>
[![Code Climate](https://codeclimate.com/github/elcodedocle/synapp/badges/gpa.svg)](https://codeclimate.com/github/elcodedocle/synapp)
## About the project

This project was completed on 2012 during an Erasmus internship on [Akademia 
Gorniczo-Hutnicza University of Science an Technology][23] (Krakow), under the 
supervision of [Professor Bipin Indurkhya][2] at [AGH-UST's Computer Science 
Department][24] and [Professor Juan Carlos Burguillo Rial][3] at the 
[Telematics Engineering Department][25] of [Universidade de Vigo][26].

Its purpose is to present an experiment consisting on a series of 
crowd-sourcing related activities through an online web platform for exploring 
and developing its user's cognitive skills related with the creative process.

The activities presented are:
 
  * Synapp's main activity, consisting on pairing 2 presented images by
  writing a word or short phrase describing a new, original artifact involving 
  both of them. Each pair of images can be presented simultaneously (one next 
  to each other), or sequentially (one after another, with a few seconds 
  delay).

  * Synapp's creativity tests, consisting on:

   - Two standard creativity tests:

     * A modified version of Guilford's alternative uses task taken from the
     Torrance Tests of Creative Thinking.

     * Wallach and Kogan's common property test.

   - A test based on a word puzzle: Will Shortz's Word Equation (Ditloid)
   Analysis test.

Synapp also acts as a crowd sourcing platform, allowing its users to evaluate
other users' responses on the activities performed and follow up the
evaluations received on their own answers. After performing an activity, a user
can evaluate other user's answers to that activity, and check her/his own
performance on the stats section.

A profile page is also available, where users can optionally provide relevant
background about themselves so it can be collected for statistical purposes and
help further study of the data obtained from the activities performed.

The main activity is performed randomly before or after the creativity tests.
Every new user will be randomly assigned to one of two existing groups. A user
can only evaluate answers provided by users assigned to a different group.

With the default configuration and bundled resources, users are randomly
assigned to one of two existing groups. Each group has a collection of
resources assigned, and there is no overlapping of resources between the two
collections.

An administrative interface provides a way for the administrators guiding the
experiment to check on the progress of any user or group of users, and it also
gives the necessary functionality for manipulation of the activities and
resources presented (such as adding, deleting, and assigning resources to
resource collections, groups of users and activities).

## Change Log

### UPDATE v1.0.5 (2015/10 - Fifth maintenance update)

 * Secure random code generation

### UPDATE v1.0.4 (2015/08 - Fourth maintenance update)

 * Migration to bitbucket

 * Code clean-up

 * Dependency upgrades

 * Full W3C HTML5 and CSS2 specs compliance.

 * synapp-vagrant-box subproject: Autodeployment of a running synappv1 webapp
 on any host machine with a simple:
 
`git clone https://bitbucket.com/synappv1/synapp-vagrant-box && vagrant up`

, using git, vagrant orchestrator and puppet provisioner.

### UPDATE v1.0.3 (2015/02 - Third maintenance update)

 * Repackaging and releasing of standalone modules and third party dependencies
 into their own repositories.
 
 * Dependency managing through [composer][30]

### UPDATE v1.0.2 (2013/07 - Second maintenance update)

 * Added optional improved password storage security through PHP >= 5.5.0's 
 [password_hash][17] function.

### UPDATE v1.0.1 (2013/04 - First maintenance update)

 * Rebuilt admin module.

### UPDATE v1.0 (2012/06 - First public domain release on https://synapp.info)

 * Added Facebook login support to login module.
 
 * HTTPS redirection of HTTP requests on production and testing

### UPDATE v0.9 (2012/04)

 * Uses and common property test activities evaluation module.

### UPDATE v0.8 (2012/03)

 * Ditloid test activity evaluation module.

### UPDATE v0.7 (2012/03)

 * Uses and common property test activities module.

### UPDATE v0.6 (2012/02)

 * Ditloid test activity module.

### UPDATE v0.5 (2012/02)

 * Improved stats module.

### UPDATE v0.4 (2012/01)

 * Client-side input validation module. 
 * Improved menu bar module.

### UPDATE v0.3 (2012/01)

 * Optimized database queries.
 * Error message handling.

### UPDATE v0.2 (2011/12)

 * SynappV1 stats module.

### v0.1 (2011/11)

 * SynappV1 login module.
 * SynappV1 user registration module.
 * SynappV1 profile edition module.
 * SynappV1 main activity module.
 * SynappV1 main activity evaluation module.

## Known Bugs

There are no currently known bugs on the application (you are welcomed to [open
a ticket][51] if you find one).

## Requirements and dependencies

All the client and server modules and 3rd party client and server dependencies 
are defined on `synapp/composer.json` and installed on `synapp/vendor/synappv1`
folder by simply running `composer install` on the project's `synapp` folder. 
The installation will retrieve the dependencies from their respective 
repositories. Each of them will include a copy of its GNU compatible license 
(In case a license is missing please open a ticket on [synapp's public 
repository][31] and it will be included). There is also a Vagrant/Puppet
project aimed to set up a virtual machine running synapp with a single
`vagrant up` command (You are welcomed to collaborate if you are interested and
know how to use vagrant for orchestrating a virtual machine with Puppet
provisioning).

Synapp's client application platform software requirements:

 * Any updated web browser with support for HTTP/1.1, ECMA5 Javascript, HTML5 
 and CSS2.

Synapp's client application platform hardware requirements:

 * Any system capable of running the mentioned browser smoothly: 1GB+ of RAM, 
 1.5GHz+ minimum (4GB 2.5GHz dual core recommended) for desktop devices; 256MB+,
 600MHz+ minimum (1GB, 1GHz recommended) for mobile devices.

 * With a network connection to the server with moderate latency and bandwidth 
 (tests show 3G 1GB/month quota plans or 1MB/112KB bandwidth asymmetric DSL 
 Internet connections comfortably fulfill all client endpoint connection 
 requirements).
 
 * A display of 10" minimum for desktop devices; 4" minimum for mobile devices,
 with a minimum resolution .

Synapp's client application makes use of the following client libraries and modules:

 * [jQuery][32] The popular javascript library to facilitate cross browser 
 compatibility, DOM manipulation and client/server communication.
 
 * [jQuery flot][33] A widely used graphs and charts generation plugin for 
 jQuery.

 * [Synapp's Open PGP javascript utility library][34], a repackaging of Herbert
 Hanewinkel's robust and thoroughly tested Open PGP AES/RSA javascript 
 implementation and encryption utility.

 * [Synapp's captcha module][35], a standalone captcha utility used by Synapp's 
 account registration and contact form modules.

 * [Synapp's client-ui resources][36], a collection of images used by Synapp's 
 web client user interface.

 * [Synapp's uuid generator][45], a [universal unique identifier][46] generator
 PHP class.

 * [Synapp's cryptosecureprng][54], which allows for secure random codes 
 generation (such as account deletion validation code or password recovery 
 validation code).

The administrative interface's HTML5 client also uses:
 
 * [Bootstrap 3][47], a CSS framework used to build the administrative layout
 
 * [Datatables][48], HTML5 table customization, for user management (along with
its [bootstrap integration plugin][50])
 
 * [CKEditor][49], HTML5 rich editor for editing dynamic content, like the FAQ
section

Synapp's server application platform hardware requirements:

 * 1GB dedicated RAM, 1 x 2.5GHz Xeon virtual core, 8GB SSD VM, with 1 Gigabit 
 Ethernet network connection**
 
** Settings vary widely depending on the expected server load. Stress
tests were performed for this configuration using different tools: 
[selenium][37] ([through PHPUnit][38]), [loadimpact][39] and [JMeter][40]; On
this server, running a LAMP stack, they show 100% load rate is reached at a 
total of approximately 200 simultaneous sessions performing requests at an 
aggregated rate of 19-20 requests per second; the average page loading time at 
this rate being approximately 5 seconds; providing [php-fpm][41] is used with 
[mod_proxy_fcgi][42] and [worker MPM][43] ([check the PHP manual][44] for set 
up instructions). The limit on this configuration is established by the 
available RAM: once swapping kicks in, the request processing rate drops 
considerably and requests start to accumulate, completely interrupting the 
service if no traffic management measures are taken (e.g. [mod_evasive][XX] 
Apache Web Server module for iptables temporary client ip ban; or 
[MaxClients][XX] and related Apache Web Server configuration directives for 
limiting the maximum number of concurrent connections; or, if using php-fpm 
with Apache mod_proxy_fcgi, implementing load balancing by, for example, 
redirecting requests to a temporary [AWS load-based instance][XX], and also 
limiting the maximum number of simultaneous requests on the php-fpm 
configuration file).
 
Synapp's server application platform software requirements are:

 * PHP >= 5.3 default configuration with [PDO][4], [GD][5], [mcrypt][6] and
 [gnupg][27] modules installed. PHP >= 5.4.0 with [curl][28] and [mbstring][29]
 modules is required for facebook login support, and PHP >= 5.5.0 is required 
 for improved password storage security through the [password_hash][17]
 function. Enabling [openssl][XX] module is also advised, since it is required
 for running composer's remote install script.
 
 * A web server with some kind of PHP CGI connector (preferably with HTTPS 
 support; [Apache Web Server][18] >= 2.2.X is recommended).

Synapp's server application module dependencies are:

 * [Facebook API for PHP] >= 4.0 In order to provide facebook login and profile
 information retrieval using [Facebook's Graph API v2.2][XX].
 
 * [Synapp's default task resources][XX], a collection of images used by 
 Synapp's main tasks.
 
 * [composer][XX] >= 1.0-dev is not a requirement per se, but it is highly 
 recommended to automatically retrieve all the dependencies, which are 
 defined in the provided `synapp/composer.json` file.

## How to install/configure/deploy

An updated LAMP stack is suggested as a platform for a standard 
deployment.

A vagrant orchestrator along with a puppet provisioner designed to meet 
synapp's development and trial requirements are provided on 
[synapp-vagrant-box][XX] repository (still under development). This box is also
fitted for production use in controlled environments (labs, conferences), where
the neither the server nor the service provided by the app are required to be
exposed directly to the Internet. The deployment process goes as follows:
 
 * Download and install Vagrant if not installed
 
 * Download and install Puppet if not installed
 
 * Download and install git if not installed
 
 * Open a command line and input the following commands: 
 
```
git clone https://butbucket.org/synappv1/synnap-vagrant-box
vagrant up
```

That's all. The machine's web server will be up and running synapp, accessible 
through http://localhost:8088/synapp

The database service will also be forwarded and accesible on `mysql://localhost:3336`

For production, a standard CentOS 6.X/7.X Server set up with LAMP features 
enabled during the installation process will usually only require the extra 
step of installing the PHP extension modules listed on the previous section to 
have it ready for deployment. 

There is a [detailed tutorial][53] available on the resources/deployment_guide 
folder of this repository which will guide you step by step on setting up an 
environment and deploying the project on an Amazon EC2 CentOS 7 instance. 
This is the recommended and most tested production environment.

For general help deploying a CentOS 6.X LAMP stack:

 * You can check out [this online tutorial][7].

[Details on how to upgrade to the latest PHP version][22] may also be useful.

For general help deploying a CentOS 7.X LAMP stack (x86-64 architecture):
 
 * You can checkout [this online tutorial][20]

For a cloud deployment, Amazon AWS Marketplace offers 
[free LAMP bundle AMIs][8] as well as [CentOS AMIs][21] ready to deploy as 
Elastic Computing Cloud (EC2) instances.

If you need further help during this step of the process, contact an 
experienced sysadmin or devops expert.

After setting up the platform's environment, follow the steps below to install,
configure and deploy the project:

* Copy the files inside `/synapp` folder to a public folder on your web 
 server (e.g. `/var/www/synapp`). (You might want to leave this as the final 
 step but it is important to keep that in mind during the next steps, 
 particularly those related to the deployment's configuration).
 
* The first thing to do is to set up and select a deployment configuration. 
By default three deployment configurations are defined: development, test and 
production. Edit and select any of these three to set up the desired 
deployment. You can also remove any of them or add/remove extra ones at will.

* The database connection parameters of each deployment configuration (host, 
port, database name, user and password) must be set by editing the provided 
file [accounts/config/database_host_and_credentials.php.dist][10] and renaming 
it as 
`accounts/config/{SYNAPP_CONFIG_DIRNAME}/database_host_and_credentials.php`

* [A SQL script intended for deployment on a MySQL/MariaDB database system 
version 5.5 or higher][9] is included on the `/resources` folder of this 
repository. It contains the SQL schema objects used by the application (A few 
extra ones per user will be dinamically generated although it is not a best 
practice. This is prevented on SynappV2 by the introduction of a much better 
design E-R model. It can be run by running `mysql -u username -p database_name
 < synappV1_schema.sql`.

* The app's credentials and login/logout redirection URLs are required by the 
facebook login module. You can set them by filling them on 
[accounts/config/facebook_credentials.php.dist][11] and renaming the file as 
`accounts/config/{SYNAPP_CONFIG_DIRNAME}/facebook_credentials.php`. (Don't 
forget to allow yourappdomain.com on your facebook app settings page. You'll 
find a link to it [here][12], where you can also create a new app and find the 
app's id and secret).

* In order to provide a minimum layer of security against packet sniffing on 
open wireless networks when SSL is not available, the password is ciphered 
using an RSA public key before being sent to the server. This public key must 
be set on the file [crypt_constants.php][13], while the private key must be 
readily available to the server on a non public folder (by default 
/var/.gnupg/www-data)

(You can generate a valid RSA keypair using OpenSSH from the command line: 
`ssh-keygen -t rsa -C "your_email@example.com"`)

* Some extra configuration parameters are provided on 
[accounts/config/profile_constants_constraints_defaults_and_selector_values.php.dist][14],
which must be renamed as
`accounts/config/{SYNAPP_CONFIG_DIRNAME}/profile_constants_constraints_defaults_and_selector_values.php`
(This is for optimization and fine tuning purposes only. Although the renamed 
file must be present, modification of the default values provided on it is not 
required for the app to run properly).


That's all! After these steps make sure your web server is running and serving 
the contents of `synapp` folder; SynappV1 web application will then be fully 
deployed and ready for use.

## How to use

* After deployment, use a browser or HTML5 client to hit the deployment
location and follow the on-screen instructions.

* The log in screen for the administrative interface will be accessible on the
route `admin/admlogin.phtml` relative to the deployment location.

* Before using the administrative interface, at least one admin user must be
created. The CLI script provided under `resources/synadmin.php` can be used
for that purpose. Running it without any parameters will display basic usage
instructions:

```
php -f synadmin.php
```

## TODO

* As of the moment of writing this paragraph (July 2012, v1.0) the development 
of this project has been restricted to bug fixes and standard 
security/maintenance tasks. A new project, SynappV2, was started with
the intention of doing a full rebuild taking advantage of the improvements 
introduced on the latest PHP versions, as well as improving modularity and 
flexibility, allowing new activities to be implemented requiring minimal to no 
extra coding efforts thanks to a new model design, a brand new administration 
module, and a REST API solution. Many independent modules of SynappV2 were
developed and are already production ready and released to their own public 
repositories (which can be found [here][15]), but the development of the V2
full stack platform has been canceled.
 
## Acknowledgments

 * Professor Juan Carlos Burguillo Rial, from Universidade de Vigo.
 * Professor Bipin Indurkhya, from AGH-UST.
 * The Erasmus EU programme for Education (particularly the supervisors Prof. 
 Raúl Fernando Rodríguez Rubio and Prof. Małgorzata Żabińska) 
 * All the staff and colleagues at University of Vigo and Akademia 
 Gorniczo-Hutnicza University of Science an Technology that made this project 
 possible.
 * My parents and brother, and the rest of my always supportive family.
 * All the beta testers who helped improve the application by providing excellent
 ideas for improving the application and pointing out all the bugs and problems
 they found.

## Want to contribute?

1. If you have a bitbucket account, [fork][52] this project
2. `git checkout -b newbranch` and `git push origin newbranch` your commits
3. Make a [pull request](https://bitbucket.org/synappv1/synapp/compare/) 
from your branch

**Q**: I am using Apache web server and I can't access the app on the specified
 location when setup finishes.  
**A**: You probably have a conflict with an existing .htaccess on your server. 
Add these two lines at the top of all your previously existent .htaccess files 
on the path to the app root, right after `RewriteEngine on` 
line:
```apache
RewriteCond %{REQUEST_URI} ^/uri/path/to/synappV1/root/folder(/.*)?$
RewriteRule (.*) $1 [L]
```


## FAQ

**Q**: Does it work on Mac OS X / any version of Microsoft Windows / any OS 
other than Linux?  
**A**: The project can be deployed on any OS supporting PHP and MySQL/MariaDB.

**Q**: Which browsers / web client rendering engines are supported?  
**A**: Any HTML5 / ECMA5 Javascript compatible browser is supported, which 
means any (reasonably up to date) mainstream browser, such as Internet 
Explorer >= 8, Chrome, Mozilla Firefox, Safari, Android Stock Browser, 
Konqueror, etc. will be supported. Latest Mozilla Firefox is the recommended 
browser, since it was the reference browser used for the development of the 
client application.

**Q**: Does it work on a web server other than Apache?  
**A**: Although Apache is officially supported and recommended, any HTTP/1.X 
compatible web server with PHP support should be fine. A PHP builtin web server
routing script is provided on the resources folder for development purposes. 


## License

This code and the libraries it depends on (3rd party and own) have 
[GPLv2][16] compatible licenses that allow commercial and non commercial use 
free of charge. Check out the [LICENSE][1] file for more specific information 
on the MIT Expat license, used on all code and resources published on synapp's 
main public repository. Check out each dependency repository for specific info 
on its GPLv2 compatible license, which should be found on the LICENSE file of 
its root folder.

[1]: https://bitbucket.org/synappv1/synapp/raw/master/LICENSE
[2]: http://www.ki.agh.edu.pl/en/staff/indurkhya-bipin
[3]: https://sites.google.com/site/jcburgui/
[4]: http://php.net/manual/en/book.mysqli.php
[5]: http://php.net/manual/en/book.image.php
[6]: https://php.net/manual/es/book.mcrypt.php
[7]: https://www.linode.com/docs/websites/lamp/lamp-server-on-centos-6
[8]: https://aws.amazon.com/marketplace/search/results/ref=dtl_navgno_search_box?page=1&searchTerms=LAMP
[9]: https://bitbucket.org/synappv1/synapp/raw/master/resources/synappV1_schema.sql
[10]: https://bitbucket.org/synappv1/synapp/raw/master/synapp/account/config/database_host_and_credentials.php.dist
[11]: https://bitbucket.org/synappv1/synapp/raw/master/synapp/account/config/facebook_credentials.php.dist
[12]: http://developers.facebook.com/apps 
[13]: https://bitbucket.org/synappv1/synapp/raw/master/synapp/account/config/crypt_constants.php
[14]: https://bitbucket.org/synappv1/synapp/raw/master/synapp/account/config/profile_constants_constraints_defaults_and_selector_values.php.dist
[15]: https://github.com/elcodedocle
[16]: http://www.gnu.org/licenses/gpl-2.0.html
[17]: http://php.net/manual/en/function.password-hash.php
[18]: http://httpd.apache.org/
[19]: https://aws.amazon.com/marketplace/pp/B00O7WM7QW
[20]: https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-centos-7
[21]: https://aws.amazon.com/marketplace/search/results/ref=srh_navgno_search_box?page=1&searchTerms=CentOS
[22]: http://blog.famillecollet.com/pages/Config-en
[23]: http://www.agh.edu.pl/en/
[24]: http://www.ki.agh.edu.pl/en
[25]: http://www-gist.det.uvigo.es
[26]: http://uvigo.es
[27]: http://php.net/manual/en/gnupg.installation.php
[28]: http://php.net/manual/es/book.curl.php
[29]: http://php.net/manual/en/book.mbstring.php
[30]: https://getcomposer.org
[31]: https://bitbucket.org/synappv1/synapp
[32]: http://jquery.com
[33]: http://www.flotcharts.org
[34]: https://bitbucket.org/synappv1/openpgpjs
[35]: https://bitbucket.org/synappv1/captcha
[36]: https://bitbucket.org/synappv1/ui-resources
[37]: http://www.seleniumhq.org
[38]: https://phpunit.de/manual/current/en/selenium.html
[39]: https://loadimpact.com
[40]: http://jmeter.apache.org
[41]: http://php-fpm.org
[44]: http://php.net/manual/en/install.fpm.php
[45]: https://bitbucket.org/synappv1/uuid
[46]: https://en.wikipedia.org/wiki/Universally_unique_identifier
[47]: http://getbootstrap.com/
[48]: https://www.datatables.net/
[49]: http://ckeditor.com/
[50]: https://github.com/DataTables/Plugins/tree/1.10.7/integration/bootstrap/3
[51]: https://bitbucket.org/synappv1/synapp/issues
[52]: https://bitbucket.org/synappv1/synapp/fork
[53]: https://synapp.info/deployment_guide/index.html
[54]: https://bitbucket.org/synappv1/cryptosecureprng

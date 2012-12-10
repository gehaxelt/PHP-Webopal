WebOpal - A webinterface for Opal
==================================

- 1. Requirements
- 2. Installation
- 3. Update

##1.Requirements##

You need to install [Opal](https://projects.uebb.tu-berlin.de/opal/trac) on your server.
You need [Sass](http://sass-lang.com/) on your server.

##2.Installation##

- 1. Clone this repository into your webservers document root.
- 2. Create the directories 'tmp'.
- 3. Make the dir 'tmp' writeable by the webserver
- 4. Rename **config.example.php** to **config.php**
- 5. Edit **config.php**
- 6. Run `make compile`
- 7. Upload it to your server

##3.Update##
- 1. Run `git pull`
- 2. Run `make compile`
- 3. Edit **config.php**
- 4. Upload it to your server

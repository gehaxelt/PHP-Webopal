WebOpal - A webinterface for Opal
==================================

- 1. Requirements
- 2. Installation
- 3. Update

##1. Minimum Requirements##

You need to install [Opal](https://projects.uebb.tu-berlin.de/opal/trac) on your server.

##2. Installation##

- 1. Clone this repository into your webservers document root.
- 2. Create the directories 'tmp'.
- 3. Make the dir 'tmp' writeable by the webserver
- 4. Rename **config.example.php** to **config.php**
- 5. Edit **config.php**
- 6. Recommended: Run `make compile` (See 4.)
- 7. Recommended: Enable gzip compression (See 5.)
- 8. Upload it to your server

##3. Update##
- 1. Run `git pull`
- 2. Recommended: Run `make compile` (See 4.)
- 3. Edit **config.php**
- 4. Upload it to your server

##4. Optimize Javascript & CSS##
###4.1. Requirements###
For Javascript Optimization you will need a [JRE](https://en.wikipedia.org/wiki/JRE) like [OpenJDK](https://openjdk.java.net/) or [Java](https://java.com)

For CSS Optimization you will need [Sass](https://sass-lang.com/)

###4.2. How to###
- For optimizing both: Run `make compile`
- For optimizing Javascript: Run `make compile-js`
- For optimizing CSS: Run `make compile-css`

###4.3. Why?###
This optimization fastenes the Speed of the PHP-Webopal on old PCs.

###4.4. What if I dont want to do this?###
If you don't want to use the optimized versions, there are fallback CSS and Javascript available, which will be chosen, if you never run `make compile`.

Please note, that once you did run `make compile` you will need to run it after each update or delete the following files:
- **js/jquery-*.min.js**
- **js/script.min.js**
- **css/style.css**

##5. Enable gzip compression##
In order to shrink server load, you are able to enable gzip compression:
- 1. Rename **.htaccess.example** to **.htaccess**
- 2. Adjust the paths in **.htaccess** (Please note that absolute Paths are necessary!)

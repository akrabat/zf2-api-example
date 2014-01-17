Simple Zend Framework 2 API example using the AbstractRestfulController
=======================================================================

Installation
------------

Using Composer
--------------

Install Composer so that it is available on your include path.

Clone this repository and manually invoke `composer`

    cd my/project/dir
    git clone git://github.com/akrabat/zf2-api-example.git
    cd zf2-api-example
    php composer.phar self-update
    php composer.phar install

(The `self-update` directive is to ensure you have an up-to-date `composer.phar`
available.)

Another alternative for downloading the project is to grab it via `curl`, and
then pass it to `tar`:

    cd my/project/dir
    curl -#L https://github.com/akrabat/zf2-api-example/tarball/master | tar xz --strip-components=1

You would then invoke `composer` to install dependencies per the previous
example.

Web Server Setup
----------------

### PHP CLI Server

The simplest way to get started if you are using PHP 5.4 or above is to start the internal PHP cli-server in the root directory:

    php -S 0.0.0.0:8080 -t public/ public/index.php

This will start the cli-server on port 8080, and bind it to all network
interfaces.

**Note: ** The built-in CLI server is *for development only*.

### Apache Setup

To setup apache, setup a virtual host to point to the public/ directory of the
project and you should be ready to go! It should look something like below:

    <VirtualHost *:80>
        ServerName zf2-tutorial.localhost
        DocumentRoot /path/to/zf2-tutorial/public
        SetEnv APPLICATION_ENV "development"
        <Directory /path/to/zf2-tutorial/public>
            DirectoryIndex index.php
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    </VirtualHost>


Test
----

Use `curl` to GET `/`  or GET `/1`.


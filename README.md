# Skills Heroes API

Project made for the Skills Heroes competition. CRUD API for fetching teacher data.

## Table of contents

1. [Setting up](#setting-up)
2. [Folder structure](#folder-structure)

## Setting up

Welcome, welcome, to my infamous setup instruction document for the
project!

I'll be guiding you through setting up everything you need to get the project
up and running.

Without further ado, let us start right away.

### What are we using?

To start off, here's a list of the things we'll be using for this guide, so
I won't need to keep specifyinig.

```txt
System:
  OS: Raspbian GNU/Linux 11 (bullseye)
  CPU: CPU: BCM2711 (4) @ 1.500GHz
  Shell: zsh 5.8
Packages:
  git (1:2.25.1-1)
  nginx (1.18.0-0)
  php (8.1.7-1)
  php-curl (8.1.10-2)
  php-fpm (8.1.10-2)
  php-mbstring (8.1.10-2)
  php-mysql (8.1.10-2)
  php-xml (8.1.10-2)
  php-zip (8.1.10-2)
Programs:
  Composer (2.3.7)
```

Things like the shell aren't important, of course, but the installed packages
and programs are crucial!

### Dependencies

To start off, let's install all dependencies.

Firstly, update your package repositories by running `sudo apt update`.

Then, install all the required packages by running `sudo apt update <the names of all packges here>`.

Next, we should install Composer. In the case that this guide goes out-of-date
due to Composer releasing a new version, head to
<https://getcomposer.org/download> for the installation instructions.

That should be all to get you set up.

### Setting up the database

Since anything using the Debian package repositories is doomed to have insanely outdated packages installed, we will
have to install MySQL by hand.

This is done as following:

```bash
wget https://dev.mysql.com/get/mysql-apt-config_0.8.22-1_all.deb
sudo dpkg -i mysql-apt-config_0.8.22-1_all.deb
sudo apt update
sudo apt install mysql-server
mysql_secure_installation
```

You can check the status of the database by running `sudo systemctl status mysql`

Like any database software, it will never ever *just work* out of the box.

If you encounter any issues, please Google them.

### Setting up the website

To get started, clone the repository to a local directory by
using `git clone https://github.com/lexisother/skills-heroes-1`.

Make sure to change your current directory to the newly created one by using
`cd skills-heroes-1`.

Now, we need to install the website dependencies. We can do this by running
`composer update`.

Next, we need to configure the website. Run `cp .env.example .env` to copy the
example file to an actual config file.

Edit this new file to suit your needs. Make sure the `DB_` values are set
correctly.

Next, simply visit the server hostname. To check if everything completed successfully, fire up your DB viewer
of choice and check out the database tables.

An SQL query like the following should suffice if you're using the command
line: <!-- see: https://stackoverflow.com/a/3914051 -->

```sql
SELECT TABLE_NAME
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_TYPE = 'BASE TABLE'
  AND TABLE_SCHEMA = 'YOUR DATABASE NAME HERE'
```

### Web server

To set up nginx correctly, we'll have to make some changes to the `/etc/nginx/sites-enabled/default` file. You can
delete it, and recreate it.

You can add the below block to the config. I have added some comments to help explain what everything does.

```nginx
# The `server` directive sets configuration for a virtual server.
server {
  # We listen on port 80.
  listen 80;
  listen [::]:80;

  # Our server name needn't be set, for it is implied. (`localhost`)
  server_name _;

  # The path to wherever your website files are.
  root /path/to/skills-heroes-1;

  # We set the response header `X-Frame-Option` to one of the two available
  # directives, namely `SAMEORIGIN`. # This means the page can only be displayed
  # in a frame on the same origin as the page itself. (so it doesn't allow
  # embedding the page in other websites)
  add_header X-Frame-Options "SAMEORIGIN";

  # The `nosniff` option for X-Content-Type-Options blocks a request if the
  # request destination is of type `style` and the MIME type is not `text/css`,
  # or of type `script` and the MIME type is not a JavaScript MIME type.
  add_header X-Content-Type-Options "nosniff";

  # We tell nginx the default file it should display is `index.php`.
  index index.php;

  # The `charset` directive adds the specified charset to the `Content-Type`
  # response header field. If this charset is different from the charset
  # specified in the source_charset directive, a conversion is performed.
  charset utf-8;

  # The `location` directive sets configuration depending on a request URI.
  # See: http://nginx.org/en/docs/http/ngx_http_core_module.html#location
  location / {
    # http://nginx.org/en/docs/http/ngx_http_core_module.html#try_files
    try_files $uri $uri/ /index.php?$query_string;
  }

  # Disable logging of access to the favicon and robots.txt, as these are very
  # commonly accessed
  location = /favicon.ico { access_log off; log_not_found off; }
  location = /robots.txt { access_log off; log_not_found off; }

  # The `~` in a `location` block denotes anything to the right side of it is
  # what we refer to as a "regular expression".
  # Regular expressions are a sequence of characters that specify a search
  # pattern in text.
  # In this case, we match everything that ends in `.php`.
  location ~ \.php$ {
    # The rest of the directives in here are related to the program we use to
    # run our PHP, as it is not done by NGINX.
    # For more information, either read the NGINX documentation or Google
    # "fastcgi"
    fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
    include snippets/fastcgi-php.conf;
  }

  # Deny access to all files that start with a dot.
  location ~ /\.(?!well-known).* {
    deny all;
  }
}
```

## Folder structure

### `Controllers`

Contains the HTTP controllers. Functionality behind routes is implemented in each controller.

### `DTO`

Contains all data transfer objects. Used for data validation.

### `Lib`

Contains utilities such as custom data types and exceptions.

### `Models`

Contains the Eloquent models. Used for interacting with DB tables.
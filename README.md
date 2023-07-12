# Installation Guide

## Introduction

**Reward Loyalty** is a Laravel PHP solution, perfect for businesses of all sizes wanting to boost customer loyalty with digital savings cards. This versatile tool is ideal for both single-retailer applications and multi-retailer setups, making it a handy resource for marketing and digital agencies.

Visit our [online documentation](https://nowsquare.com/en-us/reward-loyalty/docs/introduction) for additional information.

## Technology Stack

This project uses the following frameworks and technologies:

 - PHP Version 8.1.0 or higher
 - Laravel Version 10.x
 - SQLite, MySQL 5.7+ or MariaDB 10.3+
 - Tailwind CSS 3.x
 - Flowbite 1.x, Tailwind CSS component library
 - Tailwind Elements 1.x, open-source UI Kit
 - NPM Vite for packaging JavaScript and CSS

Refer to the `composer.json` file in the root directory for additional PHP libraries, and the `package.json` file for JavaScript libraries.

## Prerequisites

Before proceeding with the installation, please ensure you meet the following requirements:

 - PHP 8.1.0 or higher
 - Apache web server
 - SQLite, MySQL 5.7+ or MariaDB 10.3+

### PHP extensions

The following extensions are essential for the proper functioning of the script. They are typically pre-installed on most hosting services and their presence will be verified during the installation process:

 - ext-bcmath, Bcmath PHP Extension
 - ext-ctype, Ctype PHP Extension
 - ext-curl, cURL PHP Extension
 - ext-dom, DOM PHP Extension
 - ext-exif, Exif PHP Extension
 - ext-fileinfo, Fileinfo PHP Extension
 - ext-filter, Filter PHP Extension
 - ext-gd, GD PHP Extension
 - ext-hash, Hash PHP Extension
 - ext-iconv, Iconv PHP Extension
 - ext-intl, Internationalization PHP Extension
 - ext-json, JSON PHP Extension
 - ext-libxml, Libxml PHP Extension
 - ext-mbstring, Mbstring PHP Extension
 - ext-openssl, OpenSSL PHP Extension
 - ext-pcre, PCRE PHP Extension
 - ext-pdo, PDO PHP Extension
 - ext-pdo_sqlite, PDO SQLite PHP Extension
 - ext-session, Session PHP Extension
 - ext-tokenizer, Tokenizer PHP Extension
 - ext-xml, XML PHP Extension
 - ext-zlib, Zlib PHP Extension

## Installation

Follow these steps for installation:

 - Upload all files to your website's root directory.
 - Access the URL where you've uploaded these files. This will prompt an installation screen.
 - Proceed with the on-screen instructions to install the script.

Post-installation, use the admin credentials to log in at <u>example.com/en-us/admin</u>. As an admin, you have the privilege to create partners, who can then generate loyalty cards and rewards.

### Localhost

To operate the script on your local environment, make use of Laravel's integrated `artisan serve` command as follows:

```php artisan serve```

## Upgrading

### Check your current version

To check the current version of the application, log in as an admin at <u>example.com/en-us/admin</u>. The version number will be displayed on the dashboard.

Additionally, a version.txt file is included in the provided zip file.

### Upgrade

In the zip file we've provided, you'll find a directory labeled `upgrade`. Inside, there are zip files named `upgrade-x.x.x-to-[version].zip`. If your current version of the script is older than the one specified in this file name, you can proceed with the upgrade. Simply extract the contents of `upgrade-x.x.x-to-[version].zip` and overwrite all the existing files in your script's web root directory.

Begin by extracting the contents from the zip file corresponding to your current version. For instance, if you're at version `1.6.1`, start with the `upgrade-1.6.x-to-[version].zip` file, and skip `upgrade-1.x.x-to-1.6.0.zip`. Then, proceed with all the subsequent upgrade zip files in the order of their version numbers.

## Troubleshooting

Ensure to review the log file, which can be found at `logs/laravel.log`.

## Conclusion

If you encounter any issues or have specific questions, don't hesitate to consult our [Support Page](https://nowsquare.com/en-us/reward-loyalty/support) for assistance.
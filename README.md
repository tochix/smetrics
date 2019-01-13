# smetrics
Generate some stats based on users posts.

Requirements
------------
- PHP 7.1 or newer.
- [Composer][composer]
- [Git][git]

Install
-------

To install clone this repo and use composer to install the dependencies:

```sh
git clone git@github.com:tochix/smetrics.git
cd smetrics
composer install
```

Usage
-----

Here's a basic usage example:

- Run the SMetrics client from your CLI

```sh
cd smetrics
php index.php
```

- Need to change config parameters? Update the config [here][config].

[composer]: https://github.com/composer/composer
[git]: https://git-scm.com/downloads
[config]: https://github.com/tochix/smetrics/blob/master/src/Config/smetrics.php

# Pubwich

## Required components

The following components must be installed so Pubwich can work:

* [Apache](http://www.apache.org)
* [PHP 5](http://www.php.net/) (with `SimpleXML` & `cURL` extensions)
* `crontab` (installed on all UNIX systems)

## Installation

1. Duplicate `cfg/config.sample.php` to `cfg/config.php`

   (optional: if you want to use a custom theme, duplicate `themes/default` to `themes/your_theme_name` and edit the `PUBWICH_THEME` constant in `cfg/config.php` to `"your_theme_name"`.

2. Edit the newly created `config.php` to fill the blank spaces with your informations (API keys, usernames, site’s URL, etc.) and to modify the arguments passed to `Pubwich::setServices()`.

3. Modify your `crontab` file (by running `crontab -e`) and add the following line:

   `*/<N> * * * * <PHP> -f <ABSOLUTE_PATH>/cron/cron.php`

   Then replace the following elements:

   * `<N>` Cache expiration (in minutes)
   * `<PHP>` The path to PHP executable binary (usually `/usr/bin/php` or `/usr/local/bin/php`, use `which php` to find it)
   * `<ABSOLUTE_PATH>` Absolute path to Pubwich directory

   Example:

   `*/10 * * * * /usr/bin/php -f /home/myusername/public_html/pubwich/trunk/cron/cron.php`
   
4. Change the permissions on the `cache` directory to make it writeable for all (`$ chmod -R 0777 cache`).

5. Everything should be working now (when browsing to the `index.php` file!).


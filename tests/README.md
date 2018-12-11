## Testing Idea Garden

Essential pre-requisites:

* Run `composer install`
* Run `bin/install-wp-tests.php <arguments>`

For the second of those commands, the arguments are (in order):

* Database name
* User
* Password
* Host
* WordPress version (or 'latest' - this is optional)
* Skip database creation (optional but can be set to 'true')

By default, the installation steps places a copy of WordPress (and the WordPress test suite) into your system's temporay 
directory. For that reason, you _may_ periodically need to repeat the installation procedure. To run the suite, working 
from within the plugin's root directory, do:

`vendor/bin/phpunit --bootstrap tests/bootstrap.php --test-suffix .php tests`

It is *possible* to use your global PHPUnit, if you have one, however the provided PHPUnit is a version which will work 
successfully until [core WP bug 43218](https://core.trac.wordpress.org/ticket/43218) is resolved.


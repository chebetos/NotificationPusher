#!/bin/bash 
wget http://cs.sensiolabs.org/get/php-cs-fixer.phar
wget http://downloads.atoum.org/nightly/mageekguy.atoum.phar
php php-cs-fixer.phar fix src
php php-cs-fixer.phar fix tests/units
php mageekguy.atoum.phar -c tests/configurations/coverage.php -d tests/units

#!/bin/bash

set -e

find . -name '*~' -exec rm -f '{}' \;

if [ -L phpunit ] ; then true ; else
    ln -s vendor/bin/phpunit phpunit
fi

perl -pi -e 's/^.*\@(category|subpackage|package).*$//sm' $(find Classes -type f)
perl -pi -e 's/^.*\* PHP version 5\s*$//sm' $(find Classes -type f)

perl -pi -e 's/http:\/\/code\.google\.com\/p\/xinc\//https:\/\/github.com\/xinc-develop\/xinc-core\//g' \
    $(find Classes -type f)
perl -pi -e 's/http:\/\/xincplus\.sourceforge\.net/https:\/\/github.com\/xinc-develop\/xinc-core\//g' \
    $(find Classes -type f)

if [ -L php-cs-fixer ] ; then true ; else
    if [ -f vendor/bin/php-cs-fixer ] ; then true ; else
        mkdir -p vendor/bin
        curl http://get.sensiolabs.org/php-cs-fixer.phar -o vendor/bin/php-cs-fixer
        chmod +x vendor/bin/php-cs-fixer
    fi
    ln -s vendor/bin/php-cs-fixer php-cs-fixer
fi

#find Classes -name '*.php' -exec ./php-cs-fixer fix '{}' \;
#./php-cs-fixer fix 

rm -rf docs/*
doxygen
mkdir -p coverage
rm -rf coverage/*
./phpunit --coverage-html coverage

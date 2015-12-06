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


rm -rf docs/*
doxygen
mkdir -p coverage
rm -rf coverage/*
./phpunit --coverage-html coverage
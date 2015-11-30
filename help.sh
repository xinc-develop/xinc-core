#!/bin/bash

set -e

if [ -L phpunit ] ; then true ; else
    ln -s vendor/bin/phpunit phpunit
fi

rm -rf docs/*
doxygen

perl -pi -e 's/^.*\@(category|package).*$//sm' $(find Classes -type f)
perl -pi -e 's/^.*\* PHP version 5\s*$//sm' $(find Classes -type f)

perl -pi -e 's/http:\/\/code\.google\.com\/p\/xinc\//https:\/\/github.com\/xinc-develop\/xinc-core\//g' \
    $(find Classes -type f)
perl -pi -e 's/http:\/\/xincplus\.sourceforge\.net/https:\/\/github.com\/xinc-develop\/xinc-core\//g' \
    $(find Classes -type f)

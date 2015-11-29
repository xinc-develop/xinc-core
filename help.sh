#!/bin/bash

set -e

if [ -L phpunit ] ; then true ; else
    ln -s vendor/bin/phpunit phpunit
fi

rm -rf docs/*
doxygen

perl -pi -e 's/^.*\@(category|package).*$//sm' $(find Classes -type f)


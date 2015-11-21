#!/bin/bash

set -e

if [ -L phpunit ] ; then true ; else
    ln -s vendor/bin/phpunit phpunit
fi


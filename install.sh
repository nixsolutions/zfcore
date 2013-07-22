#!/bin/sh
#############################################################################
# Installation
#
# Small installation script
#
#############################################################################

# Server data
echo "Permissions for ./data/"

chmod a+w ./data/cache/     && echo " ./cache/"
chmod a+w ./data/logs/      && echo " ./logs/"
chmod a+w ./data/languages/ && echo " ./languages/"
chmod a+w ./data/session/   && echo " ./session/"
chmod a+w ./data/uploads/   && echo " ./uploads/"

# Public data
echo "Permissions for ./public/"
chmod a+w ./public/captcha/ && echo " ./captcha/"
chmod a+w ./public/uploads/ && echo " ./uploads/"

# Copy .htaccess file
echo "Copy .htaccess file"
cp ./public/.htaccess.sample ./public/.htaccess

#Loading composer and dependencies
echo "Downloading composer"
curl -sS https://getcomposer.org/installer | php

echo "Installing dependencies"
php composer.phar install

# Binary
chmod a+x ./bin/zfc.sh

# Run migration
#cd ./bin/
#./zfc.sh up migration

echo "done"
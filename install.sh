#!/bin/sh
#############################################################################
# Installation
#
# Small installation script
#
#############################################################################

# Server data
echo "Permissions for ./data/"

chmod a+w ./data/cache/   && echo " ./cache/"
chmod a+w ./data/logs/    && echo " ./logs/"
chmod a+w ./data/session/ && echo " ./session/"
chmod a+w ./data/uploads/ && echo " ./uploads/"

# Public data
echo "Permissions for ./public/"
chmod a+w ./public/captcha/ && echo " ./captcha/"
chmod a+w ./public/uploads/ && echo " ./uploads/"


# Binary
#chmod a+x ./bin/zf.sh
#chmod a+x ./bin/zfc.sh

# Run migration
#cd ./bin/
#./zfc.sh up migration

echo "done"
#!/bin/sh
#############################################################################
# Installation
#
# Small installation script
#
#############################################################################

# Server data
mkdir ./data/cache/ -m 0777
mkdir ./data/logs/ -m 0777
mkdir ./data/session/ -m 0777
mkdir ./data/uploads/ -m 0777

# Public data
mkdir -p ./public/images/captcha/ -m 0777
mkdir -p ./public/uploads/ -m 0777

# Binary
chmod a+x ./bin/zf.sh
chmod a+x ./bin/zfc.sh

# Run migration
cd ./bin/
./zfc.sh up migration
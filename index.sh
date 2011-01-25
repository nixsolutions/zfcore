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
mkdir ./public/images/captcha/ -m 0777
mkdir ./public/uploads/ -m 0777
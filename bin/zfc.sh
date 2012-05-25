#!/bin/sh

APP_DIR="$( cd "$( dirname $( dirname "${BASH_SOURCE[0]}" ) )" && pwd )"

ZF_CONFIG_FILE="$APP_DIR/.zf.ini"
export ZF_CONFIG_FILE

ZEND_TOOL_INCLUDE_PATH_PREPEND="$APP_DIR/library"
export ZEND_TOOL_INCLUDE_PATH_PREPEND

zf $@

#!/bin/sh
ZF_CONFIG_FILE="./../.zf.ini"
export ZF_CONFIG_FILE

ZEND_TOOL_INCLUDE_PATH_PREPEND="./../library"
export ZEND_TOOL_INCLUDE_PATH_PREPEND

zf $@

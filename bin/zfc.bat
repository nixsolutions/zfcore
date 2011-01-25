@ECHO off
SET ZF_CONFIG_FILE=./../.zf.ini
SET ZEND_TOOL_INCLUDE_PATH_PREPEND=./../library

./zf.bat -- %*

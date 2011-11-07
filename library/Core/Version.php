<?php

/**
 * Core_Version
 *
 * get current tag
 * tip - latest changeset
 * for correct working update live servers only to tags
 */
class Core_Version
{
    /**
     * @var string
     */
    static protected $_version = null;

    /**
     * get version
     *
     * @static
     * @return string
     */
    static public function getVersion()
    {
        if (null === self::$_version) {
            self::$_version = trim(`hg parent | grep tag: | awk '{print($2)}'`);
        }
        return self::$_version;
    }
}

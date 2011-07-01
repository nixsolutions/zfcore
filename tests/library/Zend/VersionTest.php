<?php
/**
 * Zend_VersionTest
 *
 * @category   Tests
 * @package    Library
 * @subpackage Zend
 * @version  $Id: VersionTest.php 3165 2011-01-13 19:49:02Z maxs $
 */
class Zend_VersionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test zend version
     */
    public function testVersion()
    {
        $this->assertTrue(version_compare(Zend_Version::VERSION, '1.11', '>='));
    }
}
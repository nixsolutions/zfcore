<?php
/**
 * Interface for Core_Mailer adapters
 * Currently only Directory and DbTable supported
 * 
 * @category   Core
 * @package    Core_Mailer
 * @subpackage Storage
 * 
 * @version  $Id: Interface.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
interface Core_Mailer_Storage_Interface
{
    
    /**
     * Fetch data from any datasource 
     * 
     * @param  string $alias
     * @return array  Array of data for Core_Mailer_template object
     */
    function getTemplate($alias);
    
}

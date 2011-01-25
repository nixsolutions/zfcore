<?php
/**
 * This is the DbTable class for the options table.
 *
 * @category Application
 * @package Model
 * @subpackage Manager
 * 
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 * 
 * @version  $Id: Manager.php 47 2010-02-12 13:17:34Z AntonShevchuk $
 */
class Model_Option_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name    = 'options';

    /** Primary Key */
    protected $_primary = 'id';
}
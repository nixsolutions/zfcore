<?php
/**
 * This is the DbTable class for the mail table.
 *
 * @category Application
 * @package Model
 * @subpackage DbTable
 * 
 * @version  $Id: Manager.php 47 2010-02-12 13:17:34Z AntonShevchuk $
 */
class Model_Mail_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name    = 'mail_templates';

    /** Primary Key */
    protected $_primary = 'id';
}
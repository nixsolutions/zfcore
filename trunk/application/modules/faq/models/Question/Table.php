<?php
/**
 * Question Table
 *
 * @category   Application
 * @package    Faq
 * @subpackage Model
 */
class Faq_Model_Question_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'faq';

    /** Primary Key */
    protected $_primary = 'id';

    /** Row Class */
    protected $_rowClass = 'Faq_Model_Question';
}
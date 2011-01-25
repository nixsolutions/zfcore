<?php
/**
 * Feedback DBTable
 *
 * @category Application
 * @package Model
 * @subpackage Feedback
 *
 * @version  $Id: Feedback.php 1561 2009-10-16 13:31:31Z dark $
 */
class Feedback_Model_Feedback_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'feedback';

    /** Primary Key */
    protected $_primary = 'id';
    
    /** Row Class */
    protected $_rowClass = 'Feedback_Model_Feedback';
}

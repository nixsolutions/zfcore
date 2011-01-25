<?php
/**
 * Model Feedback
 *
 * @category Application
 * @package Model
 *
 * @version  $Id: Feedback.php 1561 2009-10-16 13:31:31Z dark $
 */
class Feedback_Model_Feedback extends Core_Db_Table_Row_Abstract
{
    const STATUS_NEW    = 'New';
    const STATUS_READ   = 'Read';
    const STATUS_REPLY  = 'Reply';
    const STATUS_EDIT   = 'Edit';
    const STATUS_DELETE = 'Delete';
}

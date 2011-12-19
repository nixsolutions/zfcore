<?php
/**
 * Translate_Model_Translate_Table
 *
 * @category Application
 * @package Model
 * @subpackage Post
 *
 * @version  $Id$
 */
class Translate_Model_Translate_Table extends Core_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'translate';

    /** Primary Key */
    protected $_primary = 'id';

    /** Row Class */
    protected $_rowClass = 'Translate_Model_Translate';
}
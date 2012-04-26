<?php
/**
 * Question Model
 *
 * @category   Application
 * @package    Faq
 * @subpackage Model
 */
class Faq_Model_Question extends Core_Db_Table_Row_Abstract
{
    /**
     * get all questions
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getQuestions()
    {
        return $this->getTable()->fetchAll();
    }

    /**
     * @see Zend_Db_Table_Row_Abstract::_insert()
     */
    protected function _insert()
    {
        $this->created = date('Y-m-d H:i:s');
        $this->_update();
    }

    /**
     * @see Zend_Db_Table_Row_Abstract::_update()
     */
    protected function _update()
    {
        $this->updated = date('Y-m-d H:i:s');
    }
}
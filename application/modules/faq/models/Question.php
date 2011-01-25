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
     * @return Core_Db_Table_Row
     */
    public function getQuestions()
    {
        return $this->getTable()->fetchAll();
    }
}
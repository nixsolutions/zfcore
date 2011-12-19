<?php
/**
 * Edit comment alias form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id: Edit.php 2011-11-21 11:59:34Z pavel.machekhin $
 */
class Comments_Model_CommentAlias_Form_Edit extends Comments_Model_CommentAlias_Form_Create
{
    /**
     * @see Zend_Form::setDefaults()
     */
    public function setDefaults($defaults)
    {
        if (isset($defaults['alias'])) {
            $this->getElement('alias')
                ->getValidator('Db_NoRecordExists')
                ->setExclude(
                    array('field' => 'alias', 'value' => $defaults['alias'])
                );
        }
        
        if (isset($defaults['options'])) {
            $this->getElement('options')
                ->setValue(
                    Zend_Json_Decoder::decode($defaults['options'])
                );
            
            unset($defaults['options']);
        }
        
        return parent::setDefaults($defaults);
    }
}
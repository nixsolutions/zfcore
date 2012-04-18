<?php
/**
 * Create comment alias form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id: CommentAlias.php 2011-11-21 11:59:34Z pavel.machekhin $
 */
class Comments_Model_CommentAlias_Form_Create extends Core_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('commentAliasForm')
            ->setMethod('post');

        $this->addElements(
            array(
                $this->_alias(),
                $this->_options(),
                $this->_countPerPage(),
                $this->_relatedTable(),
                $this->_submit()
            )
        );
    }
    
    /**
     * Create alias element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _alias()
    {
        $element = new Zend_Form_Element_Text('alias');
        $element->setLabel('Alias')
                 ->addDecorators($this->_inputDecorators)
                 ->setRequired(true)
                 ->addFilter('StringTrim')
                 ->addValidator(
                     'Db_NoRecordExists',
                     false,
                     array(
                         array('table' => 'comment_aliases', 'field' => 'alias')
                     )
                 );

        return $element;
    }
        
    /**
     * Create options element
     *
     * @return Zend_Form_Element_MultiCheckbox
     */
    protected function _options()
    {
        $element = new Zend_Form_Element_MultiCheckbox(
            'options', 
            array(
                'multiOptions' => array(
                    'keyRequired' => ' Key Required',
                    'preModerationRequired' => ' Pre-moderation',
                    'titleDisplayed' => ' Title Displayed',
                    'paginatorEnabled' => ' Page Navigation'
                )
            )
        );
        $element->addDecorators($this->_inputDecorators);
        $element->setLabel('Options');

        return $element;
    }
    
    /**
     * Create countPerPage element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _countPerPage()
    {
        $element = new Zend_Form_Element_Text('countPerPage');
        $element->setLabel('Items per page')
                 ->addDecorators($this->_inputDecorators)
                 ->setRequired(false)
                 ->addValidator(
                     'Digits'
                 );

        return $element;
    }
    
    /**
     * Create relatedTable element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _relatedTable()
    {
        $element = new Zend_Form_Element_Text('relatedTable');
        $element->setLabel('Related table')
                 ->addDecorators($this->_inputDecorators)
                 ->setRequired(false)
                 ->setFilters(array('StringTrim', 'StringToLower'))
                 ->addValidator(
                     'Db_NoRecordExists',
                     false,
                     array(
                         array('table' => 'comment_aliases', 'field' => 'relatedTable')
                     )
                 );;

        return $element;
    }

    
    /**
     *
     * @param string $tableName
     * @return bool
     */
    protected function _validateRelatedTable($tableName)
    {
        $adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        try {
        
            try {
                $result = $adapter->describeTable($tableName);

                if (!isset($result['comments'])) {
                    throw new Zend_Form_Exception('Column `comments` is not exist in this table');
                }

                return true;
            
            } catch (Zend_Form_Exception $e) {
                throw new Zend_Form_Exception($e->getMessage());
            } catch (Exception $e) {
                throw new Zend_Form_Exception("Table `$tableName` is not exist");
            }
        } catch (Zend_Form_Exception $e) {
            $this->getElement('relatedTable')->addError($e->getMessage());
            
            return false;
        } catch (Exception $e) {
            $this->getElement('relatedTable')->addError('Unknown error');
            
            return false;
        }
    }
    
    public function isValid($data)
    {
        $valid = parent::isValid($data);
        
        if (!empty($data['relatedTable'])) {
            $relatedTableValid = $this->_validateRelatedTable($data['relatedTable']);
            
            if (!$relatedTableValid) {
                $valid = false;
            }
        }
        
        return $valid;
    }
}
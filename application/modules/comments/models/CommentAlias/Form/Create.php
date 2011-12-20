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
class Comments_Model_CommentAlias_Form_Create extends Zend_Form
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
                $this->_submit()
            )
        );
    }
    
    /**
     * Create user login element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _alias()
    {
        $element = new Zend_Form_Element_Text('alias');
        $element->setLabel('Alias')
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
        
    protected function _options()
    {
        $element = new Zend_Form_Element_MultiCheckbox(
            'options', 
            array(
                'multiOptions' => array(
                    'keyRequired' => 'Key Required',
                    'preModerationRequired' => 'Pre-moderation',
                    'titleDisplayed' => 'Title Displayed',
                    'paginatorEnabled' => 'Page Navigation'
                )
            )
        );
        $element->setLabel('Options');

        return $element;
    }
    
    /**
     * Create user login element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _countPerPage()
    {
        $element = new Zend_Form_Element_Text('countPerPage');
        $element->setLabel('Items per page')
                 ->setRequired(false)
                 ->addValidator(
                     'Digits'
                 );

        return $element;
    }
    
    /**
     * Create submit element
     *
     * @return Zend_Form_Element_Submit
     */
    protected function _submit()
    {
        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel('Save');
        $element->setOrder(100);

        return $element;
    }
}
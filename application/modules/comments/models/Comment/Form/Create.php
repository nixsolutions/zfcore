<?php
/**
 * Create comment form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id: Comment.php 2011-11-21 11:59:34Z pavel.machekhin $
 */
class Comments_Model_Comment_Form_Create extends Zend_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setAction('/comments/add');
        $this->setMethod('post');

        $this->addElements(
            array(
                $this->_title(),
                $this->_body(),
                $this->_alias(),
                $this->_returnUrl(),
                $this->_key(),
                $this->_submit()
            )
        );
    }
    
    public function setUser($user)
    {
        if (!$user) {
            $this->setAction('/login');
            
            $this->getElement('submit')->setLabel('Add comment as ...');
        }
    }
    
    protected function _title()
    {
        $this->addElement(
            'text', 'title',
            array(
                'order'      => 10,
                'label'      => 'Comment title:',
                'cols'       => '50',
                'rows'       => '5',
                'required'   => true,
                'filters'    => array('StringTrim', 'HtmlEntities'),
                'validators' => array(
                    array('validator' => 'StringLength', 'options' => array(1, 250))
                )
            )
        );
        
        return $this;
    }
    
    protected function _body()
    {
        $element = new Zend_Form_Element_Textarea(
            'body',
            array(
                'order'      => 20,
                'label'      => 'Your comment:',
                'cols'       => '50',
                'rows'       => '5',
                'required'   => true,
                'filters'    => array('StringTrim', 'HtmlEntities'),
                'validators' => array(
                    array('validator' => 'StringLength', 'options' => array(1, 250))
                )
            )
        );
        
        return $element;
    }
    
    protected function _alias()
    {
        $element = new Zend_Form_Element_Hidden('alias');
        $element->setRequired(true)
            ->setDecorators(array('ViewHelper'));
        
        return $element;
    }
    
    protected function _returnUrl()
    {
        $element = new Zend_Form_Element_Hidden('returnUrl');
        $element->setRequired(true)
            ->setDecorators(array('ViewHelper'));
        
        return $element;
    }
    
    protected function _key()
    {
        $element = new Zend_Form_Element_Hidden('key');
        $element->setRequired(true)
            ->setDecorators(array('ViewHelper'));
        
        return $element;
    }
    
    protected function _submit()
    {
        $element = new Zend_Form_Element_Submit(
            'submit',
            array(
                'order' => 50,
                'label' => 'Add comment',
            )
        );
        
        return $element;
    }
    
    public function setAlias($alias)
    {
        $this->getElement('alias')->setValue($alias);
        
        return $this;
    }
    
    public function setReturnUrl($url)
    {
        $this->getElement('returnUrl')->setValue($url);
        
        return $this;
    }
    
    public function setKey($key)
    {
        $this->getElement('key')->setValue($key);
        
        return $this;
    }
    
    public function removeTitleElement()
    {
        $this->removeElement('title');
        
        return $this;
    }
}
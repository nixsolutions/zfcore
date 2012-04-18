<?php
/**
 * Create comment form
 *
 * @category Application
 * @package Model
 * @subpackage Form
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
    
    /**
     * Create title element
     *
     * @return Zend_Form_Element_Text
     */
    protected function _title()
    {
        $element = new Zend_Form_Element_Text('title',
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
        
        return $element;
    }
    
    /**
     * Create body element
     *
     * @return Zend_Form_Element_Textarea
     */
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
                    array('validator' => 'StringLength', 'options' => array(5, 400))
                )
            )
        );
        $element->setAttrib('class', 'span9');
        
        return $element;
    }
    
    /**
     * Create alias element
     *
     * @return Zend_Form_Element_Hidden
     */
    protected function _alias()
    {
        $element = new Zend_Form_Element_Hidden('alias');
        $element->setRequired(true)
            ->setDecorators(array('ViewHelper'));
        
        return $element;
    }
    
    /**
     * Create returnUrl element
     *
     * @return Zend_Form_Element_Hidden
     */
    protected function _returnUrl()
    {
        $element = new Zend_Form_Element_Hidden('returnUrl');
        $element->setRequired(true)
            ->setDecorators(array('ViewHelper'));
        
        return $element;
    }
    
    /**
     * Create key element
     *
     * @return Zend_Form_Element_Hidden
     */
    protected function _key()
    {
        $element = new Zend_Form_Element_Hidden('key');
        $element->setRequired(true)
            ->setDecorators(array('ViewHelper'));
        
        return $element;
    }
    
    /**
     * Create submit element
     *
     * @return Zend_Form_Element_Submit
     */
    protected function _submit()
    {
        $element = new Zend_Form_Element_Submit(
            'submit',
            array(
                'order' => 50,
                'label' => 'Add comment',
            )
        );
        $element->setAttrib('class', 'btn btn-primary');
        
        return $element;
    }
    
    /**
     * Set the value for the `alias` element
     * 
     * @param string $alias
     * @return Comments_Model_Comment_Form_Create 
     */
    public function setAlias($alias)
    {
        $this->getElement('alias')->setValue($alias);
        
        return $this;
    }
    
    /**
     * Set the value for the `returnUrl` element
     * 
     * @param string $url
     * @return Comments_Model_Comment_Form_Create 
     */
    public function setReturnUrl($url)
    {
        $this->getElement('returnUrl')->setValue($url);
        
        return $this;
    }
    
    /**
     * Set the value for the `key` element
     * 
     * @param string $key
     * @return Comments_Model_Comment_Form_Create 
     */
    public function setKey($key)
    {
        $this->getElement('key')->setValue($key);
        
        return $this;
    }
    
    /**
     * Remove `title` element
     * 
     * @return Comments_Model_Comment_Form_Create 
     */
    public function removeTitleElement()
    {
        $this->removeElement('title');
        
        return $this;
    }
}
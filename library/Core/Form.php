<?php
/**
 * Form prepared for work with ZFCore+Bootstrap
 *
 * @category Core
 * @package  Form
 *
 * @author   dark
 * @created  10.04.12 16:38
 */
class Core_Form extends Zend_Form
{

    protected $_inputDecorators = array(
        array('HtmlTag', array('tag' => 'dd', 'class'=>'control-group form-inline')),
        array('Label', array('tag' => 'dt', 'class'=>'control-group')),
        array('Errors', array('class'=>'help-inline')),
    );

    /**
     * Create submit element
     *
     * @return Zend_Form_Element_Submit
     */
    protected function _submit()
    {
        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel('Save');
        $element->setAttrib('class','btn btn-primary span1');
        $element->setOrder(100);

        return $element;
    }
}

<?php

/** Zend_Form_Element */
require_once 'Zend/Form/Element.php';

/**
 * Redactor form element
 *
 * @category Core
 * @package  Core_Form_Element_Redactor
 *
 * <code>
 *
 * required jQuery.js
 *
 * using as form element:
 *
 * $comment = new Core_Form_Element_Redactor('comment', array(
 *    'label' => 'Your comment:',
 *    'cols'  => '50',
 *    'rows'  => '5',
 *    'required' => true,
 *    'filters' => array('StringTrim'),
 *    'redactor' => array(...)
 * ));
 * $form->addElement($comment);
 *
 * $form->addElement('redactor', 'comment', array(
 *    'label' => 'Your comment:',
 *    'cols'  => '50',
 *    'rows'  => '5',
 *    'required' => true,
 *    'filters' => array('StringTrim'),
 *    'redactor' => array(..)
 * ));
 *
 * using as view helper:
 *
 * echo $view->formRedactor('comment', 'ula la', array(
 *    'label' => 'Your comment:',
 *    'cols'  => '50',
 *    'rows'  => '5',
 *    'redactor' => array(...)
 * ));
 *
 * </code>
 *
 */
class Core_Form_Element_Redactor extends Zend_Form_Element
{
    /**
     * view helper
     *
     * @var string
     */
    public $helper = 'formRedactor';
}

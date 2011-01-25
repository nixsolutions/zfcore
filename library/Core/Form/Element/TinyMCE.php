<?php

/** Zend_Form_Element */
require_once 'Zend/Form/Element.php';

/**
 * TinyMCE form element
 * 
 * @category Core
 * @package  Core_Form_Element_TinyMCE
 *
 * @author Ivan Nosov aka rewolf <i.k.nosov@gmail.com>
 * 
 * @version  $Id: TinyMCE.php 162 2010-07-12 14:58:58Z AntonShevchuk $
 * 
 * <code>
 * 
 * before using this element need prepare jQuery.js and tinyMCE.js
 * 
 * using as form element:
 * 
 * $comment = new Core_Form_Element_TinyMCE('comment', array(
 *    'label' => 'Your comment:',
 *    'cols'  => '50',
 *    'rows'  => '5',
 *    'required' => true,
 *    'filters' => array('StringTrim'),
 *    'tinyMCE' => array(
 *        'mode' => "textareas",
 *        'theme' => "simple",
 *    ),
 * ));
 * $form->addElement($comment);
 * 
 * if you want pass a form element type to the form object
 * you need add view helper path
 * $view->addHelperPath('Core/View/Helper', 'Core_View_Helper');
 * 
 * $form->addElement('tinyMCE', 'comment', array(
 *    'label' => 'Your comment:',
 *    'cols'  => '50',
 *    'rows'  => '5',
 *    'required' => true,
 *    'filters' => array('StringTrim'),
 *    'tinyMCE' => array(
 *        'mode' => "textareas",
 *        'theme' => "simple",
 *    ),
 * ));
 * 
 * using as view helper:
 * 
 * before using in view need add view helper path
 * 
 * $view->addHelperPath('Core/View/Helper', 'Core_View_Helper');
 * echo $view->formTinyMCE('Comment', 'ula la', array(
 *    'label' => 'Your comment:',
 *    'cols'  => '50',
 *    'rows'  => '5',
 *    'class' => 'mceSimple',
 *    'tinyMCE' => array(
 *        'mode' => "textareas",
 *        'theme' => "simple",
 *    ),
 * ));
 *
 * </code>
 * 
 */
class Core_Form_Element_TinyMCE extends Zend_Form_Element
{
    /**
     * Use formTextarea view helper by default
     * @var string
     */
    public $helper = 'formTinyMCE';
    
    /**
     * Set the view object
     *
     * @param  Zend_View_Interface $view
     * @return Zend_Form_Element
     */
    public function setView(Zend_View_Interface $view = null)
    {
        if (null !== $view) {
            if (false === $view->getPluginLoader('helper')->getPaths('Core_View_Helper')) {
                $view->addHelperPath('Core/View/Helper', 'Core_View_Helper');
            }
        }
        return parent::setView($view);
    }
}

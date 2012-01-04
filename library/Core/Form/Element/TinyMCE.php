<?php
/**
 * Copyright (c) 2012 by PHP Team of NIX Solutions Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * TinyMCE form element
 *
 * @category Core
 * @package  Core_Form
 * @subpackage Element
 *
 * @author   Ivan Nosov aka rewolf <i.k.nosov@gmail.com>
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
     *
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
            if (false === $view->getPluginLoader( 'helper' )->getPaths( 'Core_View_Helper' )) {
                $view->addHelperPath( 'Core/View/Helper', 'Core_View_Helper' );
            }
        }
        return parent::setView( $view );
    }
}

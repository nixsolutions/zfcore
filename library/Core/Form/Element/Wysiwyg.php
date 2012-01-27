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
 * Redactor form element
 *
 * @category Core
 * @package  Core_Form
 * @subpackage Element
 *
 * <code>
 *
 * required jQuery.js
 *
 * using as form element:
 *
 * $comment = new Core_Form_Element_Wysiwyg('comment', array(
 *    'label' => 'Your comment:',
 *    'cols'  => '50',
 *    'rows'  => '5',
 *    'required' => true,
 *    'filters' => array('StringTrim'),
 *    'wysiwyg' => array(...)
 * ));
 * $form->addElement($comment);

 * using as view helper:
 *
 * echo $view->formWysiwyg('comment', 'ula la', array(
 *    'label' => 'Your comment:',
 *    'cols'  => '50',
 *    'rows'  => '5',
 *    'wysiwyg' => array(...)
 * ));
 *
 * </code>
 *
 */
class Core_Form_Element_Wysiwyg extends Zend_Form_Element
{
    const TOOLBAR1 = 'toolbar1';

    const TOOLBAR2 = 'toolbar2';
    /**
     * view helper
     *
     * @var string
     */
    public $helper = 'formWysiwyg';

    /**
     * Add toolbar
     *
     * @param array|string $buttons
     * @return Core_Form_Element_Wysiwyg
     */
    public function addToolbar($buttons)
    {
        if (!$toolbars = $this->getAttrib('toolbars')) {
            $toolbars = array();
        }
        $toolbars[] = $buttons;

        $this->setAttrib('toolbars', $toolbars);
        return $this;
    }
}

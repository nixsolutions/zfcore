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
 * Helper to generate a "Redactor" element
 *
 * @category   Core
 * @package    Core_View
 * @subpackage Helper
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
class Core_View_Helper_FormWysiwyg extends Zend_View_Helper_FormTextarea
{
    /**
     * Helper to generate a "Redactor" element
     *
     * @param      $name
     * @param null $value
     * @param null $attribs
     * @return string
     */
    public function formWysiwyg($name, $value = null, $attribs = null)
    {
        $view = $this->view;

        $info = $this->_getInfo($name, $value, $attribs);
        $id = $view->escape($info['id']);

        $options = array('css' => $view->baseUrl('scripts/jquery/wysiwyg/frame/default.css'));
        if (!empty($attribs['editor'])) {
            $options = array_merge($options, $attribs['editor']);
            unset($attribs['editor']);
        }

        $toolbars = array();
        if (!empty($attribs['toolbars'])) {
            $toolbars = array_merge($toolbars, $attribs['toolbars']);
            unset($attribs['toolbars']);
        }

        /** add plugin libraries */
        $view->plugins()->wysiwyg();

        ob_start();
        ?>
        (function($){
            $(function(){
                $("#<?php echo $id?>").wysiwyg(<?php echo Zend_Json::encode($options)?>)
                <?php foreach ($toolbars as $toolbar):?>
                    .wysiwyg("toolbar", <?php echo Zend_Json::encode(array('buttons' => $toolbar))?>)
                <?php endforeach;?>
            });
        })(jQuery)
        <?php

        $view->headScript()->appendScript(ob_get_clean());

        /** render text area */
        return $this->formTextarea($name, $value, $attribs);
    }
}

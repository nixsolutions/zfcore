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
 * Helper to generate a "tinyMCE" element
 *
 * @category   Core
 * @package    Core_View
 * @subpackage Helper
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
 * echo $view->formTinyMCE('�omment', 'ula la', array(
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
class Core_View_Helper_FormTinyMCE extends Zend_View_Helper_FormElement
{
    /**
     * The default number of rows for a textarea.
     *
     * @access public
     *
     * @var int
     */
    public $rows = 10;

    /**
     * The default number of columns for a textarea.
     *
     * @access public
     *
     * @var int
     */
    public $cols = 50;

    /**
     * Generates a 'tinyMCE' element.
     *
     * @access public
     *
     * @param string|array $name    If a string, the element name.  If an
     *                              array, all other parameters are ignored, and the array elements
     *                              are extracted in place of added parameters.
     *
     * @param mixed        $value   The element value.
     *
     * @param array        $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    public function formTinyMCE($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo( $name, $value, $attribs );
        extract( $info );

        /** build js code */
        if (!empty($attribs['tinyMCE'])) {
            $this->view->headScript()->captureStart();
            echo '$(function (){tinyMCE.init(' . json_encode( $attribs['tinyMCE'] ) . ');});';
            $this->view->headScript()->captureEnd();
            unset($attribs['tinyMCE']);
        }

        /** disabled? */
        $disabled = '';
        if ($disable) {
            $disabled = ' disabled="disabled"';
        }

        /** add rows and cols */
        if (empty($attribs['rows'])) {
            $attribs['rows'] = (int)$this->rows;
        }
        if (empty($attribs['cols'])) {
            $attribs['cols'] = (int)$this->cols;
        }

        /** build the element */
        $xhtml = '<textarea name="' . $this->view->escape( $name ) . '"'
            . ' id="' . $this->view->escape( $id ) . '"'
            . $disabled
            . $this->_htmlAttribs( $attribs ) . '>'
            . $this->view->escape( $value ) . '</textarea>';

        return $xhtml;
    }
}

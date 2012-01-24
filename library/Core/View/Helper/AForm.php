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
 * Core_View_Helper_AForm
 *
 * @category   Core
 * @package    Core_View
 * @subpackage Helper
 * @author sm
 */
class Core_View_Helper_AForm extends Zend_View_Helper_FormElement
{
    /**
     * @var Zend_Form
     */
    protected $_form;

    /**
     * @var array
     */
    protected $_ajaxParams;

    /**
     * @var string
     */
    protected $_aFormPath;

    /**
     * Set form
     *
     * @param Zend_Form_Element $element
     * @return Lizard_Crud_View_Helper_AForm
     */
    public function aForm(Zend_Form $form = null)
    {
        if ($form) {
            $this->_form = $form;
        }
        return $this;
    }

    /**
     * Set ajax configuration
     *
     * @param array  $params
     * @param string $scriptPath
     * @return Lizard_Crud_View_Helper_AForm
     */
    public function ajax(array $params, $scriptPath = 'scripts/jquery/jquery.aForm.js')
    {
        $this->_ajaxParams = $params;

        $this->_aFormPath = $scriptPath;

        return $this;
    }

    /**
     * Render form
     *
     * @return string
     */
    public function __toString()
    {
        $view = $this->view;

        $rows = array();
        foreach ($this->_form as $element) {
            $label = null;
            if ($labelText = $element->getLabel()) {
                if ($element instanceof Zend_Form_Element_Submit) {
                    $element->setValue($labelText);
                } else {
                    $label = $view->formLabel($element->getName(), $labelText, $element->getAttribs());
                }
            }
            $el = $view->aElement($element);

            $errorList = null;
            if ($errors = $element->getMessages()) {
                $errorList = $view->formErrors($errors);
            }
            $rows[] = '<div class="form-title">' . $label . '</div>'
                . '<div class="form-field">' . $el . '</div>'
                . $errorList;
        }

        $list = $view->htmlList(
            $rows,
            false,
            array('class' => 'form-list'),
            false
        );

        $output = $view->form($this->_form->getName(), $this->_form->getAttribs(), $list);

        if ($this->_ajaxParams) {
            $id = $this->_form->getName();
            $view->headScript()->appendFile($view->baseUrl($this->_aFormPath));

            $view->headScript()->appendScript(
                '$(function(){ $("#' . $id . '").aForm(' . Zend_Json::encode($this->_ajaxParams) . ') })'
            );
        }

        return $output;
    }
}

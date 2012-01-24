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
 * Core_View_Helper_AElement
 *
 * @category   Core
 * @package    Core_View
 * @subpackage Helper
 * @author sm
 */
class Core_View_Helper_AElement extends Zend_View_Helper_FormElement
{
    /**
     * Render element
     *
     * @param Zend_Form_Element $element
     * @return string
     */
    public function aElement(Zend_Form_Element $element)
    {
        $viewClasses = array($element->getAttrib('class'));

        if ($element->isRequired()) {
            if (!$element->getAttrib('title')) {
                $element->setAttrib('title', 'Field is required');
            }
            $viewClasses[] = 'aForm-field-required';
        }

        if ($element->getValidators()) {
            $viewClasses[] = 'aForm-field-validate';
        }
        if ($element->hasErrors()) {
            $viewClasses[] = 'aForm-field-invalid';
        } elseif ($element->getValue() || !$element->isRequired()) {
            $viewClasses[] = 'aForm-field-valid';
        }
        if ($element->getValidators()) {
            $viewClasses[] = 'aForm-field-validate';
        }

        $element->setAttrib('class', implode(' ', $viewClasses));

        $options = null;
        $separator = null;
        if ($element instanceof Zend_Form_Element_Multi) {
            $options = $element->getMultiOptions();
            $separator = $element->getSeparator();
        }
        return $this->view->{$element->helper}(
            $element->getName(),
            $element->getValue(),
            $element->getAttribs(),
            $options,
            $separator
        );
    }
}

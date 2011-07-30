<?php
/**
 * Core_View_Helper_AElement
 *
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
        } elseif ($element->getValue()) {
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

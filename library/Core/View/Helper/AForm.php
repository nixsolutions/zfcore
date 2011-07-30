<?php
/**
 * Core_View_Helper_AForm
 *
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
                '$(function(){ $("#'. $id .'").aForm(' . Zend_Json::encode($this->_ajaxParams) . ') })'
            );
        }

        return $output;
    }
}

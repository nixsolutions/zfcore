<?php
/**
 * @see Zend_Dojo_Form
 */
require_once 'Zend/Dojo/Form.php';

/**
 * Menus_Model_Menu_Form_Create
 *
 * @category    Application
 * @package     Menus_Model_Menu
 * @subpackage  Form
 *
 * @author      Valeriu Baleyko <baleyko.v.v@gmail.com>
 * @copyright   Copyright (c) 2010 NIX Solutions (http://www.nixsolutions.com)
 */
class Menus_Model_Menu_Form_Create extends Zend_Dojo_Form
{

	protected $_menuManager = null;

        public function init()
        {
		$this->_menuManager = new Menus_Model_Menu_Manager();

		$this->setName('menuItemCreateForm');
                $this->setMethod('post');

		$label = new Zend_Dojo_Form_Element_TextBox('label');
		$label->setLabel('Label')
                    ->setRequired(true)
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                     ->setAttribs(array('style'=>'width:30%;margin-bottom:10px;'));


		$parent = new Zend_Dojo_Form_Element_ComboBox('parent');
		$parent->setLabel('Parent Menu Item')
                    ->setRequired(true)
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setAttribs(array('style'=>'margin-bottom:5px;'));
		$parent->addMultiOptions($this->_menuManager->getMenuItemsForEditForm());


                $title = new Zend_Dojo_Form_Element_TextBox('title');
		$title->setLabel('Title')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setAttribs(array('style'=>'width:30%;margin-bottom:5px;'));

                $class = new Zend_Dojo_Form_Element_TextBox('class');
		$class->setLabel('Class')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setAttribs(array('style'=>'width:30%;margin-bottom:5px;'));

                $rel = new Zend_Dojo_Form_Element_TextBox('rel');
		$rel->setLabel('Rel')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setAttribs(array('style'=>'width:30%;margin-bottom:5px;'));

                $rev = new Zend_Dojo_Form_Element_TextBox('rev');
		$rev->setLabel('Rev')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setAttribs(array('style'=>'width:30%;margin-bottom:5px;'));

                $active = new Zend_Dojo_Form_Element_ComboBox('active');
		$active->setLabel('Active')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setAttribs(array('style'=>'margin-bottom:5px;'));
               $active->addMultiOptions(array(1 => 'Yes', 0 => 'No'));

                $visible = new Zend_Dojo_Form_Element_ComboBox('visible');
		$visible->setLabel('Visibility')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setAttribs(array('style'=>'margin-bottom:5px;'));
                $visible->addMultiOptions(array(1 => 'Visible', 0 => 'Hidden'));

                $target = new Zend_Dojo_Form_Element_ComboBox('target');
		$target->setLabel('Target')
                    ->setRequired(true)
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                     ->setAttribs(array('style'=>'margin-bottom:5px;'));
                 $target->addMultiOptions($this->_menuManager->getTargetOptionsForEditForm());

                $module = new Zend_Dojo_Form_Element_ComboBox('module');
		$module->setLabel('MVC Module')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setAttribs(
                            array(
                                'style'=>'width:30%;margin-bottom:5px;',
                                'onChange' => '(function(name){
                                    if (name == "default") {name = "default"}
dijit.byId("controller").store = new dojo.data.ItemFileReadStore(
   {url: "/admin/menu/controllers/name/"+name});}(this.value))'
                             )
                   );

                $module->addMultiOptions($this->_menuManager->getModules());



                $controller = new Zend_Dojo_Form_Element_ComboBox('controller');
		$controller->setLabel('MVC Controller')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setAttribs(
                            array(
                                'style'=>'width:30%;margin-bottom:5px;',
                                'onChange' => '(function(name){
if (name == "default") {name = "default"}
dijit.byId("action").store = new dojo.data.ItemFileReadStore(
   {url: "/admin/menu/actions/name/"+name});}(this.value))'
                                )
                    );

                //$controller->addMultiOptions($this->_menuManager->getControllersByModuleName());

                $action = new Zend_Dojo_Form_Element_ComboBox('action');
		$action->setLabel('MVC Action')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setAttribs(array('style'=>'width:30%;margin-bottom:5px;'));
               // $action->addMultiOptions($this->_menuManager->getActionsByControllerName());

                $route = new Zend_Dojo_Form_Element_TextBox('route');
		$route->setLabel('MVC Route')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setAttribs(array('style'=>'width:50%;margin-bottom:5px;'));

                $params = new Zend_Dojo_Form_Element_TextBox('rev');
		$rev->setLabel('Rev')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setAttribs(array('style'=>'width:30%;margin-bottom:5px;'));

                $uri = new Zend_Dojo_Form_Element_TextBox('uri');
		$uri->setLabel('URI')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setAttribs(array('style'=>'width:50%;margin-bottom:5px;'));


                $id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);

                if (!empty($id)) {

                    $menuRow = $this->_menuManager->getRowById($id);
                    if ($menuRow instanceof Zend_Db_Table_Row_Abstract) {
                        $label->setValue($menuRow->getLabel());
                        $parent->setValue($menuRow->getParent());
                        $title->setValue($menuRow->getTitle());
                        $class->setValue($menuRow->getClass());
                        $target->setValue($menuRow->getTarget());
                      //  $module->setValue($menuRow->getModule());
                      //  $controller->setValue($menuRow->getController());
                      //  $action->setValue($menuRow->getAction());
                        $route->setValue($menuRow->getRoute());
                        $uri->setValue($menuRow->getUri());
                        $visible->setValue($menuRow->getVisible());
                        $submit->setLabel('Save');


                        $this->setLegend($menuRow->getTitle() . ' - Menu Edit Form');
                    }
		} else {


		}

		$this->addElements(array(

                    $label,
                    $parent,
                    $title,
                    $class,
                    $target,
                    $rel,
                    $rev,

                    $module,
                    $controller,
                    $action,
                    $route,

                    $uri,

                    $visible,
                    $active,
                    $this->_submit()
                    ));
                return $this;
        }


	public function _submit()
	{
            $this->setLegend('Menu Add Form');
            $submit = new Zend_Dojo_Form_Element_SubmitButton('submit');
            $submit->setLabel('Create');
            return $submit;
	}
}

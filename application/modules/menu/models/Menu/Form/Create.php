<?php
/**
 * @see Zend_Dojo_Form
 */
require_once 'Zend/Dojo/Form.php';

/**
 * Menu_Model_Menu_Form_Create
 *
 * @category    Application
 * @package     Menu_Model_Menu
 * @subpackage  Form
 *
 * @author      Valeriu Baleyko <baleyko.v.v@gmail.com>
 * @author      Alexander Khaylo <alex.khaylo@gmail.com>
 * @copyright   Copyright (c) 2011 NIX Solutions (http://www.nixsolutions.com)
 */
class Menu_Model_Menu_Form_Create extends Zend_Dojo_Form
{

    protected $_menuManager = null;

    public function init()
    {
        $this->_menuManager = new Menu_Model_Menu_Manager();

        $this->setName('menuItemCreateForm');
        $this->setMethod('post');

        $label = new Zend_Dojo_Form_Element_TextBox('label');
        $label->setLabel('Label')
              ->setRequired(true)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->setAttribs(array('style'=>'width:30%;margin-bottom:10px;'));

        $linkType = new Zend_Dojo_Form_Element_FilteringSelect('linkType');
        $linkType->setLabel('Type')
                 ->setRequired(true)
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim')
                 ->setAttribs(
                     array(
                         'style'=>'margin-bottom:5px;',
                         'onChange' => "changedLinkType(document.getElementById('linkType'));"
                     )
                 );
        $linkType->addMultiOptions(array(Menu_Model_Menu::TYPE_URI => 'Link', Menu_Model_Menu::TYPE_MVC => 'Route'));

        $parent = new Zend_Dojo_Form_Element_FilteringSelect('parent');
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

        $active = new Zend_Dojo_Form_Element_FilteringSelect('active');
        $active->setLabel('Active')
               ->addFilter('StripTags')
               ->addFilter('StringTrim')
               ->setAttribs(array('style'=>'margin-bottom:5px;'));
        $active->addMultiOptions(array(0 => 'No', 1 => 'Yes'));

        $visible = new Zend_Dojo_Form_Element_FilteringSelect('visible');
        $visible->setLabel('Visibility')
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setAttribs(array('style'=>'margin-bottom:5px;'));
        $visible->addMultiOptions(array(1 => 'Visible', 0 => 'Hidden'));

        $target = new Zend_Dojo_Form_Element_FilteringSelect('target');
        $target->setLabel('Target')
               ->setRequired(true)
               ->addFilter('StripTags')
               ->addFilter('StringTrim')
               ->setAttribs(array('style'=>'margin-bottom:5px;'));
        $target->addMultiOptions(
            array(
                0 => 'Don\'t set',
                Menu_Model_Menu::TARGET_BLANK  => "New window",
                Menu_Model_Menu::TARGET_PARENT => "Parrent frame",
                Menu_Model_Menu::TARGET_SELF   => "Current window",
                Menu_Model_Menu::TARGET_TOP    => "New window without frames"
            )
        );

        $route = new Zend_Dojo_Form_Element_ComboBox('route');
        $route->setLabel('Route')
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->setAttribs(
                  array(
                      'style'=>'margin-bottom:5px;',
                      'onChange' => "changedRoute(document.getElementById('route'));"
                  )
              );
        $route->addMultiOptions($this->_menuManager->getNamesOfRoutes());

        $uri = new Zend_Dojo_Form_Element_TextBox('uri');
        $uri->setLabel('URI')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttribs(array('style'=>'width:50%;margin-bottom:5px;'));

        $itemId = new Zend_Form_Element_Hidden('id');
        $itemId->addFilter('StripTags')->addFilter('StringTrim');

        $id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);

        if (!empty($id)) {
            $menuRow = $this->_menuManager->getRowById($id);
            if ($menuRow instanceof Zend_Db_Table_Row_Abstract) {
                $label->setValue($menuRow->getLabel());
                $parent->setValue($menuRow->getParent());
                $title->setValue($menuRow->getTitle());
                $class->setValue($menuRow->getClass());
                $target->setValue($menuRow->getTarget());
                $route->setValue($menuRow->getRoute());
                $uri->setValue($menuRow->getUri());
                $visible->setValue($menuRow->getVisible());
                $linkType->setValue($menuRow->getLinkType());
                $active->setValue($menuRow->getActive());
                $itemId->setValue($id);
            }
        }

        $this->addElements(
            array(
                $label,
                $parent,
                $title,
                $class,
                $target,
                $linkType,
                $route,
                $uri,
                $visible,
                $active,
                $this->_submit()
            )
        );
        return $this;
    }

    public function _submit()
    {
            $submit = new Zend_Dojo_Form_Element_SubmitButton('submit');
            $submit->setLabel('Create');
            return $submit;
    }
}

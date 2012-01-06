<?php
/**
 * Menu_Model_Menu_Form_Create
 *
 * @category    Application
 * @package     Menu
 * @subpackage  Form
 *
 * @author      Valeriu Baleyko <baleyko.v.v@gmail.com>
 * @author      Alexander Khaylo <alex.khaylo@gmail.com>
 * @copyright   Copyright (c) 2012 NIX Solutions (http://www.nixsolutions.com)
 */
class Menu_Model_Menu_Form_Create extends Zend_Form
{

    protected $_menuManager = null;

    public function init()
    {
        $this->_menuManager = new Menu_Model_Menu_Manager();

        $this->setName('menuItemCreateForm');
        $this->setMethod('post');

        $label = new Zend_Form_Element_Text('label');
        $label->setLabel('Label')
              ->setRequired(true)
              ->addFilter('StringTrim');

        $linkType = new Zend_Form_Element_Select('linkType');
        $linkType->setLabel('Type')
                 ->setRequired(true)
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim')
                 ->setAttribs(
                     array(
                         'onChange' => "changedLinkType();"
                     )
                 );
        $linkType->addMultiOptions(array(Menu_Model_Menu::TYPE_URI => 'Link', Menu_Model_Menu::TYPE_MVC => 'Route'));

        $parent = new Zend_Form_Element_Select('parent');
        $parent->setLabel('Parent Menu Item')
               ->setRequired(true)
               ->addFilter('StripTags')
               ->addFilter('StringTrim');
        $parent->addMultiOptions($this->_menuManager->getMenuItemsForEditForm());


        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Tooltip')
              ->addFilter('StringTrim');

        $class = new Zend_Form_Element_Text('class');
        $class->setLabel('CSS class')
              ->addFilter('StringTrim');

        $active = new Zend_Form_Element_Select('active');
        $active->setLabel('Active')
               ->addFilter('StripTags')
               ->addFilter('StringTrim');
        $active->addMultiOptions(array(0 => 'No', 1 => 'Yes'));

        $visible = new Zend_Form_Element_Select('visible');
        $visible->setLabel('Visibility')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $visible->addMultiOptions(array(1 => 'Visible', 0 => 'Hidden'));

        $target = new Zend_Form_Element_Select('target');
        $target->setLabel('Target')
               ->setRequired(true)
               ->addFilter('StripTags')
               ->addFilter('StringTrim');
        $target->addMultiOptions(
            array(
                0 => 'Don\'t set',
                Menu_Model_Menu::TARGET_BLANK  => "New window",
                Menu_Model_Menu::TARGET_PARENT => "Parrent frame",
                Menu_Model_Menu::TARGET_SELF   => "Current window",
                Menu_Model_Menu::TARGET_TOP    => "New window without frames"
            )
        );

        $route = new Zend_Form_Element_Select('route');
        $route->setLabel('Route')
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->setAttribs(
                  array(
                      'onChange' => "changedRoute();"
                  )
              );
        $route->addMultiOptions($this->_menuManager->getNamesOfRoutes());

        $uri = new Zend_Form_Element_Text('uri');
        $uri->setLabel('URI')
            ->addFilter('StringTrim');

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
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Create');
            $submit->setValue('Create');
            return $submit;
    }
}

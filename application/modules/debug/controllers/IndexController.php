<?php
/**
 * IndexController for debug module
 *
 * @category   Application
 * @package    Debug
 * @subpackage Controller
 *
 * @author Anna Pavlova <pavlova.anna@nixsolutions.com>
 *
 * @version  $Id$
 */

class Debug_IndexController extends Core_Controller_Action
{
    public function indexAction()
    {
        $multiform = new Core_Form_Multipage();
        $multiform->setNamespace('Core_Form_Multipage');

        $filedOne = new Zend_Form_Element_Text('field1');
        $filedOne->setLabel('Field #1')
               ->setRequired(true);

        $filedTwo = new Zend_Form_Element_Text('field2');
        $filedTwo->setLabel('Field #2')
               ->setRequired(true)
               ->addValidator('Alnum');

        $filedThree = new Zend_Form_Element_Text('field3');
        $filedThree->setLabel('Field #3')
               ->setRequired(true);

        $subFormOne = new Zend_Form_SubForm();
        $subFormOne->addElements(array($filedOne));

        $subFormTwo = new Zend_Form_SubForm();
        $subFormTwo->addElements(array($filedTwo));

        $subFormThree = new Zend_Form_SubForm();
        $subFormThree->addElements(array($filedThree));

        $multiform->addSubForm($subFormOne, 'step1', 1);
        $multiform->addSubForm($subFormTwo, 'step2', 2);
        $multiform->addSubForm($subFormThree, 'step3', 3);
        
        //$multiform->reset();
        
        echo "<pre>";
        print_r($multiform->getCurrent());
        echo "</pre>";
        exit(1);
        
        $this->assertEquals('step1', $multiform->getCurrent()->getName());
    }
}


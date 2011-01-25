<?php
/**
 * Test for Core_Form_Multipage class
 *
 * @category   Tests
 * @package    Core
 * @subpackage Core_Form
 *
 * @author     Dmitriy Britan <dmitriy.britan@nixsolutions.com>
 */
class Core_Form_MultipageTest extends ControllerTestCase
{
    protected $_fixture = array(
        '0' => array('step1' => array('field1' => '1')),
        '1' => array('step2' => array('field2' => '1')),
        '2' => array('step3' => array('field3' => '1')),
        '3' => array(),
        '4' => array('step2' => array('field2' => '')),
        '5' => array('broken' => array('field2' => '')),
    );

    public function getMultiForm()
    {
        $multiform = new Core_Form_Multipage();
        $multiform->setNamespace('Core_Form_Multipage');

        $field1 = new Zend_Form_Element_Text('field1');
        $field1->setLabel('Field #1')
               ->setRequired(true);

        $field2 = new Zend_Form_Element_Text('field2');
        $field2->setLabel('Field #2')
               ->setRequired(true)
               ->addValidator('Alnum');

        $field3 = new Zend_Form_Element_Text('field3');
        $field3->setLabel('Field #3')
               ->setRequired(true);

        $subForm1 = new Zend_Form_SubForm();
        $subForm1->addElements(array($field1));

        $subForm2 = new Zend_Form_SubForm();
        $subForm2->addElements(array($field2));

        $subForm3 = new Zend_Form_SubForm();
        $subForm3->addElements(array($field3));

        $multiform->addSubForm($subForm1, 'step1', 1);
        $multiform->addSubForm($subForm2, 'step2', 2);
        $multiform->addSubForm($subForm3, 'step3', 3);

        return $multiform;
    }

    public function testGetSessionNamespace()
    {
        $multiform = new Core_Form_Multipage();
        $multiform->setNamespace('Core_Form_Multipage');

        $session = $multiform->getSessionNamespace();

        $this->assertEquals('Core_Form_Multipage', $session->getNamespace());
    }
    
    public function testReset()
    {
        $multiform = $this->getMultiForm();

        $multiform->isValid($this->_fixture['0']);
        $multiform->isValid($this->_fixture['1']);
        $multiform->isValid($this->_fixture['2']);

        $multiform->reset();

        $this->assertFalse(
            isset($multiform->getSessionNamespace()->step1)
            || isset($multiform->getSessionNamespace()->step2)
            || isset($multiform->getSessionNamespace()->step3)
        );
    }

    public function testGetCurrentFirstWithOutStep()
    {
        $multiform = $this->getMultiForm();
        $this->assertEquals('step1', $multiform->getCurrent()->getName());
    }

    public function testGetCurrentNotFirstWithOutStep()
    {
        $multiform = $this->getMultiForm();
        $multiform->getSessionNamespace()->step1 = array('field1' => '1');

        $this->assertEquals('step2', $multiform->getCurrent()->getName());
    }

    public function testGetCurrentFirstWithStepFail()
    {
        $multiform = $this->getMultiForm();
        $multiform->setCurrent('step3');

        $this->assertEquals('step1', $multiform->getCurrent()->getName());
    }

    public function testGetCurrentNotFirstWithStepFail()
    {
        $multiform = $this->getMultiForm();
        $multiform->getSessionNamespace()->step1 = array('field1' => '1');
        $multiform->setCurrent('step3');

        $this->assertEquals('step2', $multiform->getCurrent()->getName());
    }

    public function testGetCurrentNotFirstWithStepSuccess()
    {
        $multiform = $this->getMultiForm();
        $multiform->getSessionNamespace()->step1 = array('field1' => '1');
        $multiform->getSessionNamespace()->step2 = array('field2' => '2');
        $multiform->setCurrent('step1');

        $this->assertEquals('step1', $multiform->getCurrent()->getName());
    }

    public function testGetCurrentNotFirstWithOutStepAllDataInSession()
    {
        $multiform = $this->getMultiForm();
        
        $multiform->getSessionNamespace()->step1 = array('field1' => '1');
        $multiform->getSessionNamespace()->step2 = array('field2' => '2');
        $multiform->getSessionNamespace()->step3 = array('field3' => '3');

        $this->assertEquals('step3', $multiform->getCurrent()->getName());
    }

    public function testIsValidFalseEmptyData()
    {
        $multiform = $this->getMultiForm();

        $this->assertFalse($multiform->isValid($this->_fixture['3']));
    }

    public function testIsValidFalseInvalidData()
    {
        $multiform = $this->getMultiForm();

        $multiform->isValid($this->_fixture['0']);
        
        $this->assertFalse($multiform->isValid($this->_fixture['4']));
    }

    public function testIsValidTrue()
    {
        $multiform = $this->getMultiForm();

        $multiform->isValid($this->_fixture['0']);
        $multiform->isValid($this->_fixture['1']);

        $this->assertTrue($multiform->isValid($this->_fixture['2']));
    }

    public function testIsValidFalse()
    {
        $multiform = $this->getMultiForm();

        $multiform->isValid($this->_fixture['0']);

        $this->assertFalse($multiform->isValid($this->_fixture['5']));
    }
    
    public function testGetSubForms()
    {
        $multiform = $this->getMultiForm();

        $subForms = $multiform->getSubForms(true);

        $expected = ($subForms[0]->getName() == 'step1')
            && ($subForms[1]->getName() == 'step2')
            && ($subForms[2]->getName() == 'step3');

        $this->assertTrue($expected);
    }

    public function testIsStored()
    {
        $multiform = $this->getMultiForm();
        $multiform->isValid($this->_fixture['0']);

        $sfStep1 = $multiform->getSubForm('step1');

        $this->assertTrue($multiform->isStored($sfStep1));
    }

    public function testExceptionEmptyNamespace()
    {
        $multiform = new Core_Form_Multipage();

        try {
            $multiform->getSessionNamespace();
        } catch (Core_Exception $e) {
            $this->assertContains(
                'Session namespace for multipage form undefined',
                $e->getMessage()
            );
        }
    }

    public function testExceptionZeroSubForms()
    {
        $multiform = new Core_Form_Multipage();

        try {
            $multiform->getCurrent();
        } catch (Core_Exception $e) {
            $this->assertContains(
                'Multipage form don\'t have any subforms',
                $e->getMessage()
            );
        }
    }
}
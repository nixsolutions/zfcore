<?php
/**
 * Contact us, form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id: Contactus.php 1561 2009-10-16 13:31:31Z dark $
 */
class Feedback_Model_Feedback_Form_Contactus extends Zend_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        // initial form
        $this->setName('contactusForm')
             ->setMethod(Zend_Form::METHOD_POST)
             ->setEnctype(Zend_Form::ENCTYPE_URLENCODED)
             ->setLegend('Contact us');
        // initial form elements
        $this->addElements(
            array(
                $this->_sender(),
                $this->_email(),
                $this->_subject(),
                $this->_message(),
                $this->_captcha(),
                $this->_button('Send', 'submit')
            )
        );

        return $this;
    }

    /**
	 * Create contact us element "sender"
	 *
	 * @return object Zend_Form_Element_Text
	 */
    protected function _sender()
    {
        $element = new Zend_Form_Element_Text('senderName');
        $element->setLabel('Your name: ')
                ->setRequired(true)
                ->setAttribs(array('id' => 'sender-name'))
                ->addValidator('StringLength', true, array('max' => '255'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        return $element;
    }

    /**
	 * Create contact us element "email"
	 *
	 * @return object Zend_Form_Element_Text
	 */
    protected function _email()
    {
        $element = new Zend_Form_Element_Text('senderEmail');
        $element->setLabel('Your e-mail: ')
                ->setRequired(true)
                ->setAttribs(array('id' => 'sender-email'))
                ->addValidator('EmailAddress')
                ->addValidator('StringLength', true, array('max' => '80'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        return $element;
    }

    /**
	 * Create contact us element "subject"
	 *
	 * @return object Zend_Form_Element_Text
	 */
    protected function _subject()
    {
        $element = new Zend_Form_Element_Text('subjectMssg');
        $element->setLabel('Subject: ')
                ->setRequired(true)
                ->setAttribs(array('id' => 'subject-mssg'))
                ->addValidator('StringLength', true, array('max' => '255'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        return $element;
    }

    /**
	 * Create contact us element "message"
	 *
	 * @return object Zend_Form_Element_Textarea
	 */
    protected function _message()
    {
        $element = new Zend_Form_Element_Textarea('senderMssg');
        $element->setLabel('Message: ')
                ->setRequired(true)
                ->setAttribs(array('id' => 'sender-mssg', 'cols' => '50', 'rows' => '10'))
                ->addValidator('StringLength', true, array('max' => '255'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        return $element;
    }

    /**
	 * Create contact us element "captcha"
	 *
	 * @return object Zend_Form_Element_Captcha
	 */
    protected function _captcha()
    {
        $options = array(
            'captcha' => 'Image', // Type
            'wordLen' => 5, // LengthКоличество генерируемых символов
            'width' => 140, // Image Width
            'height' => 40, // Image Height
            'timeout' => 120, // TTL of session
            'expiration' => 300, // TTL of file
            'font' => APPLICATION_PATH . '/../data/fonts/Glasten_Bold.ttf', // full path to font
            'imgDir' => APPLICATION_PATH . '/../public/images/captcha/', // full path to images
            'imgUrl' => '/images/captcha/', // URL of folder
            'gcFreq' => 5, // frequency of garbage collector
            'fontSize' => 20 // in px
        );
        $element = new Zend_Form_Element_Captcha('captcha', array('captcha' => $options));
        $element->setLabel('Code on picture: ')
                ->setIgnore(true);

        return $element;
    }

    /**
	 * Create form element "button"
	 *
	 * @param string $label - label button
	 * @param string $type  - type button
	 * @param string $name  - name/id button
	 * @return object Zend_Form_Element_Button
	 */
    protected function _button($label = 'Button', $type = 'button', $name = '')
    {
        if ( !strlen($name) ) {
            $name = str_replace(' ', '-', strtolower($label));
        }
        $element = new Zend_Form_Element_Button($name);
        $element->setLabel($label)
                ->setRequired(false)
                ->setIgnore(true)
                ->setAttribs(array('type' => $type, 'id' => $name));

        return $element;
    }
}

<?php
/**
 * Mail_Form_Template_Send
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id: Send.php 210 2010-11-23 12:17:03Z andreyalek $
 */
class Mail_Form_Template_Send extends Mail_Form_Template_Create
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('mailSendForm')->setMethod('post');

        $this->addElements(
            array(
                $this->_subject(),
                $this->_body(),
                $this->_fromName(),
                $this->_fromEmail(),
                $this->_toName()->setRequired(true),
                $this->_toEmail()->setRequired(true),
                $this->_submit()
            )
        );
    }

    /**
     * Create submit element
     *
     * @return object Zend_Form_Element_Submit
     */
    protected function _submit()
    {
        return parent::_submit()->setLabel('Send');
    }
}
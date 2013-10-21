<?php

class Payments_PaypalControllerTest extends ControllerTestCase
{

    public function testCallbackAction()
    {
        //GET (method is not allowed)
        $this->dispatch('/payments/paypal/callback');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('error');
        $this->assertContains('<b>Message:</b> Page not found</p>', $this->getResponse()->getBody());
    }


    public function testCallbackActionIncorrectTxnId()
    {
        //POST (incorrect txn_id)
        $this->request
             ->setMethod('POST')
             ->setPost(array('txn_id' => 123));
        $this->dispatch('/payments/paypal/callback');
        $this->assertModule('index');
        $this->assertController('error');
        $this->assertAction('error');
        $this->assertContains('<b>Message:</b> Page not found</p>', $this->getResponse()->getBody());
    }

}

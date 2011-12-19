<?php
/**
 * IndexController for feedback module
 *
 * @category   Application
 * @package    Dashboard
 * @subpackage Controller
 *
 * @version  $Id$
 */
class Feedback_IndexController extends Core_Controller_Action
{
    /**
     * Default action
     */
    public function indexAction()
    {
        $contactusForm = new Feedback_Model_Feedback_Form_Contactus();
        $contactusForm->setAction($this->view->url(array('action' => 'send')));
        $this->view->contactusForm = $contactusForm;
    }

    /**
     * Send message
     */
    public function sendAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $contactusForm = new Feedback_Model_Feedback_Form_Contactus();
            $contactusForm->setAction($this->view->url(array('action' => 'send')));
            if (!$contactusForm->isValid($request->getPost())) {
                $this->view->contactusForm = $contactusForm;
            } else {
                $data = $contactusForm->getValues();
                $table = new Feedback_Model_Feedback_Table();
                $message = 'Your message has been sent to administrator.';
                try {
                    $table->insert(
                        array(
                            'sender'  => $data['senderName'],
                            'email'   => $data['senderEmail'],
                            'subject' => $data['subjectMssg'],
                            'message' => $data['senderMssg'],
                            'status'  => Feedback_Model_Feedback::STATUS_NEW,
                            'created' => date('Y-m-d H:i:s')
                        )
                    );
                } catch (Exception $e) {
                    $message = 'Can not send your message. Try again later.';
                }
                $this->view->messages = $message;
            }
        } else {
            $this->_helper->redirector('index');
        }
    }
}

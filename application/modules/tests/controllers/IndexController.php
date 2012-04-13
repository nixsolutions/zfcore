<?php
/**
 * @category   Application
 * @package    Index
 * @subpackage Controller
 */
class Tests_IndexController extends Core_Controller_Action
{

    public function indexAction()
    {

    }
    public function controlsAction()
    {

    }
    public function markupAction()
    {

    }
    public function ajaxAction()
    {
        $data = array(
            // messages
            '_messages' => array(
                'info' => array(
                    'First Info Message',
                    'Second Info Message'
                ),
                'error' => array(
                    'First Error Message',
                    'Second Error Message'
                ),
                'success' => array(
                    'First Success Message',
                    'Second Success Message'
                ),
            ),
            // reload current page
            // 'reload' => true,
            // redirect to any url
            // 'redirect' => '/',
        );

        echo $this->_helper->json($data);
    }
}




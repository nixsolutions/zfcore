<?php
/**
 * IndexController for faq module
 *
 * @category   Application
 * @package    Faq
 * @subpackage Controller
 */
class Faq_IndexController extends Core_Controller_Action
{
    public function indexAction()
    {
        $faq = new Faq_Model_Question();
        $this->view->question = $faq->getQuestions();
    }
}
<?php
/**
 * MessageControllerTest
 *
 * @category   Tests
 * @package    Feedback
 */
class Feedback_ManagementControllerTest extends ControllerTestCase
{

    /**
     * set up environment
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        //parent::migrationUp('feedback');
    }

    /**
     * Setup TestCase
     */
    public function setUp()
    {
        parent::setUp();
        parent::_doLogin(Users_Model_User::ROLE_ADMIN);

        $this->_modelFeedback = new Feedback_Model_Feedback();
        $this->_tableFeedback = new Feedback_Model_Feedback_Table();

        // fixture['feedback']
        $this->_fixture['feedback'] = array(
            'sender'  => 'test',
            'subject' => 'test',
            'message' => 'test',
            'email'   => 'dark@nixsolutions.com',
            'status'  => Feedback_Model_Feedback::STATUS_NEW,
            'created' => date('Y-m-d H:i:s')
        );
        $this->_feedback = $this->_tableFeedback->createRow($this->_fixture['feedback']);
        $this->_feedback->save();

        $this->_fixture['FileImage'] = array(
            'inputFile' =>
                array(
                    'name' =>  'st_bar_top_blue.gif',
                    'type' =>  'image/gif',
                    'tmp_name' =>  '/tmp/phplEse5g',
                    'error' =>  0,
                    'size' =>  '899',
                ));
    }

    /**
     * Feedback/Index/Index
     *
     * allow access for admin
     */
    public function testFeedbackIndexAction()
    {
        $this->dispatch('/feedback/management');

        $this->assertModule('feedback');
        $this->assertController('management');
        $this->assertAction('index');
        $this->assertNotRedirect();
        $this->assertResponseCode(200);

    }
    public function testFeedbackReplyNotPostAction()
    {
        $this->getRequest()
             ->setMethod('GET')
             ->setParams(
                 array(
                     'id' => $this->_feedback->id
                 )
             );

        $this->dispatch('/feedback/management/reply');

        $this->assertRedirect();

    }

    public function testFeedbackReadNotExistingIdAction()
    {
        $this->getRequest()
             ->setMethod('POST')
             ->setPost(
                 array(
                    'id' => '11110000'
                 )
             );

        $this->dispatch('/feedback/management/read');
        $this->assertResponseCode(500);

    }

    public function testFeedbackReadValidAction()
    {
        $this->getRequest()
             ->setMethod('POST')
             ->setPost(
                 array(
                    'id' => $this->_feedback->id
                 )
             );

        $this->dispatch('/feedback/management/read');
        $this->assertModule('feedback');
        $this->assertController('management');
        $this->assertAction('read');
        $this->assertNotRedirect();
        $this->assertResponseCode(200);
       // $this->assertQueryCount('#feedbackForm', 1);

    }

/*
    public function testFeedbackEditAction()
    {
        $this->_fixture['feedback']['id'] = $this->_feedback->id;
        $this->getRequest()
             ->setMethod('POST')
             ->setPost($this->_fixture['feedback']);

        $this->dispatch('/feedback/management/edit');

        $this->assertRedirect('feedback/index');
    }
*/
/*
    public function testFeedbackEditNullIdAction()
    {
        $this->getRequest()
             ->setMethod('POST')
             ->setPost($this->_fixture['feedback']);

        $this->dispatch('/feedback/management/edit');

        $this->assertRedirect('feedback/index');
    }
*/
/*
    public function testFeedbackEditViewFormAction()
    {
        $this->_fixture['feedback']['id'] = $this->_feedback->id;
        $this->_fixture['feedback']['viewForm'] = 1;
        $this->getRequest()
             ->setMethod('POST')
             ->setPost($this->_fixture['feedback']);

        $this->dispatch('/feedback/management/edit');

        $this->assertModule('feedback');
        $this->assertController('management');
        $this->assertAction('edit');
        $this->assertNotRedirect();
        $this->assertResponseCode(200);
    }
*/
    public function testReplyViewFormAction()
    {
        $this->_fixture['feedback']['id'] = $this->_feedback->id;
        $this->_fixture['feedback']['viewForm'] = 1;
        $this->getRequest()
             ->setMethod('POST')
             ->setPost($this->_fixture['feedback']);

        $this->dispatch('/feedback/management/reply');

        $this->assertModule('feedback');
        $this->assertController('management');
        $this->assertAction('reply');
        $this->assertNotRedirect();
        $this->assertResponseCode(200);
    }

    public function testReplyNotPostAction()
    {
        $this->_fixture['feedback']['id'] = 0;
        $this->getRequest()
             ->setMethod('GET')
             ->setParams($this->_fixture['feedback']);

        $this->dispatch('/feedback/management/reply');

        $this->assertRedirect();
    }

    public function testReplyValidAction()
    {

        $this->_fixture['feedback']['id'] = $this->_feedback->id;
        $this->_fixture['feedback']['saveCopy'] = 1;
        $this->_fixture['feedback']['email'] = 'test@domain.com';
        unset($this->_fixture['feedback']['status'], $this->_fixture['feedback']['created']);
        $_FILES = array(
            'inputFile' =>
                array(
                    'name' =>  '',
                    'type' => null,
                    'tmp_name' =>  '',
                    'error' =>  4,
                    'size' => null
                ));
        $this->getRequest()
             ->setMethod('POST')
             ->setPost($this->_fixture['feedback']);

        $this->dispatch('/feedback/management/reply');

        $this->assertRedirect();

    }
    /**
     * tear Down
     *
     */
    public function tearDown()
    {
        //$this->_tableFeedback = new Model_Feedback_Manager();
//        var_dump($this->_tableFeedback);
//        $this->_tableFeedback->deleteById($this->_feedback->id);
        parent::tearDown();
    }


    public static function tearDownAfterClass()
    {
        //parent::migrationDown('feedback');
        parent::tearDownAfterClass();
    }
}

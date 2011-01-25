<?php
/**
 * IndexControllerTest
 * 
 * @category Tests
 * @package  Sync
 */
class Sync_IndexControllerTest extends ControllerTestCase
{
    /**
     * set up environment
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        parent::migrationUp('pages');
    }

    public function setUp()
    {
        parent::setUp();

        //Testing tables
        $this->_userTable = new Model_User_Table();
        $this->_pagesTable = new Pages_Model_Page_Table();

        $this->_fixture['testuser'] = array(
            'login' => 'username_' . time(),
            'email' => 'username_' . time() . '@ukr.net',
            'password' => 'test1234',
            'status' => 'active',
        );

        $this->_fixture['testpage'] = array(
            'title'   => 'Test'.date('Y-m-d H:i:s'),
            'alias'   => 'test'.date('Y-m-d H:i:s'),
            'content' => 'Content, content',
            'user_id' => 1,
            'pid'     => 1
        );

        $this->_fixture['testpage2'] = array(
            'title'   => 'Test2'.date('Y-m-d H:i:s'),
            'alias'   => 'test2'.date('Y-m-d H:i:s'),
            'content' => 'Content2, content2',
            'user_id' => 1,
            'pid'     => 1
        );
    }

    public function testSyncActionSuccess()
    {
        $config = new Zend_Config_Ini(
            $this->getFrontController()->getModuleDirectory('sync') .
            '/configs/sync.ini',
            APPLICATION_ENV
        );

        if ($config->tables) {
            $user = $this->_userTable->create($this->_fixture['testuser']);
            $user->save();

            $pages = $this->_pagesTable->create($this->_fixture['testpage']);
            $pages->updated = date('Y-m-d H:i:s');
            $pages->save();

            $postData = array(
                'security' => array(
                    'username' => $this->_fixture['testuser']['login'],
                    'password' => $this->_fixture['testuser']['password']
                )
            );

            $this->request->setMethod('POST')->setPost($postData);
            $this->dispatch('/sync/');

            $this->assertHeader('Content-Type', 'application/xml');
            $this->assertXpath('/response[@result="success"]');
            $this->assertXpath('/response/table[@name="pages"]');
            $this->assertXpathCount('/response/table[@name="pages"]/item', 1);
            $this->assertXpathContentContains(
                '/response/table[@name="pages"]/item/content',
                htmlspecialchars($this->_fixture['testpage']['content'])
            );
        
            $pages->delete();
            $user->delete();
        }
    }

    public function testSyncActionSuccessWithUpdated()
    {
        $config = new Zend_Config_Ini(
            $this->getFrontController()->getModuleDirectory('sync') .
            '/configs/sync.ini',
            APPLICATION_ENV
        );

        if ($config->tables) {
            $user = $this->_userTable->create($this->_fixture['testuser']);
            $user->save();

            $pages = array();

            $pages['0'] = $this->_pagesTable->create(
                $this->_fixture['testpage']
            );
            $pages['0']->updated = date('2010-09-01 14:00:00');
            $pages['0']->save();
            
            $pages['1'] = $this->_pagesTable->create(
                $this->_fixture['testpage2']
            );
            $pages['1']->updated = date('2010-09-02 14:00:00');
            $pages['1']->save();

            $postData = array(
                'security' => array(
                    'username' => $this->_fixture['testuser']['login'],
                    'password' => $this->_fixture['testuser']['password']
                 ),
                'updated' => strtotime('2010-09-01 19:00:00'),
            );

            $this->request
                ->setMethod('POST')
                ->setPost($postData);

            $this->dispatch('/sync/');

            $this->assertHeader('Content-Type', 'application/xml');
            $this->assertXpath('/response[@result="success"]');
            $this->assertXpath('/response/table[@name="pages"]');
            $this->assertXpathCount('/response/table[@name="pages"]/item', 1);
            $this->assertXpathContentContains(
                '/response/table[@name="pages"]/item/content',
                htmlspecialchars($this->_fixture['testpage2']['content'])
            );

            $pages['0']->delete();
            $pages['1']->delete();
            $user->delete();
        }
    }

    public function testSyncActionFailed()
    {
        $config = new Zend_Config_Ini(
            $this->getFrontController()->getModuleDirectory('sync') .
            '/configs/sync.ini',
            APPLICATION_ENV
        );

        if ($config->tables) {
            $user = $this->_userTable->create($this->_fixture['testuser']);
            $user->save();

            $pages = $this->_pagesTable->create($this->_fixture['testpage']);
            $pages->updated = date('Y-m-d H:i:s');
            $pages->save();

            $postData = array(
                'security' => array(
                    'username' => 'some_custom_login_',
                    'password' => $this->_fixture['testuser']['password']
                )
            );

            $this->request
                ->setMethod('POST')
                ->setPost($postData);
            $this->dispatch('/sync/');

            $this->assertHeader('Content-Type', 'application/xml');
            $this->assertXpath('/response[@result="failed"]');
            $this->assertXpath('/response/message');

            $pages->delete();
            $user->delete();
        }
    }
    public static function tearDownAfterClass()
    {
        parent::migrationDown('pages');
        parent::tearDownAfterClass();
    }
}
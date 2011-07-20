<?php
/**
 * PagesTest
 *
 * @category Tests
 * @package  Model
 */
class Model_ImageTest extends ControllerTestCase
{
    /**
     * fixtures
     *
     * @var array
     */
    protected $_data;

    /**
     * model instance
     *
     * @var object
     */
    private $_model;

    public function setUp()
    {
        parent::setUp();

        $this->_clean($this->_data['basePath'], $this->_data['uploadDir']);

        $uid = date('YmdHis');

        $this->_data = array(
            'basePath'         => realpath('.') . '/',
            'uploadDir'        => 'temp'.$uid,
            'targetHidden'     => 'temp'.$uid.'/.thumb',
            'targetDir'        => 'temp'.$uid.'/thumb',
            'targetFileFull'   => 'temp'.$uid.'/temp.png',
            'targetFile'       => 'temp.png',
            'thumbSizes'       => array(1 => array('width'  => 160,
                                                    'height' => 120),
                                         2 => array('width'  => 64,
                                                    'height' => 64)),
                                                    );

        $this->_uploadFile = array(
            'correct'   => array('size'     => 12346,
                                'name'     => 'testfile.jpg',
                                'tmp_name' => 'asasdf',
                                'error'    => 0,
                                'type'     => 'image/png'),
            'incorrect' => array('size'     => 123460000,
                                 'name'     => 'asdfasdf',
                                 'name2'    => 'asd.psd',
                                 'name3'    => 'temp.png',
                                 'error'    => 5,
                                 'type'     => 'application'));


        mkdir($this->_data['basePath'] . $this->_data['uploadDir']);
        mkdir($this->_data['basePath'] . $this->_data['targetDir']);
        mkdir($this->_data['basePath'] . $this->_data['targetHidden']);
        touch($this->_data['basePath'] . $this->_data['targetFileFull']);

        $this->_model = new Pages_Model_Image($this->_data);
    }

    /**
     * clean directory
     *
     * @param string $path
     * @param string $entry
     */
    private function _clean($path, $entry)
    {
        if (is_dir($path . $entry)) {
            $dir = scandir($path . $entry);
            foreach ($dir as $subEntry) {
                if (($subEntry !== '.') && ($subEntry !== '..')) {
                    $this->_clean($path . $entry . '/', $subEntry);
                }
            }
            rmdir($path . $entry);
        } elseif (is_file($path . $entry)) {
            unlink($path . $entry);
        }
    }
    /**
     * Test get default directory option
     *
     */
    function test_getDefaultDir()
    {
        $this->assertEquals(
            $this->_data['uploadDir'],
            $this->_model->getDefaultDir()
        );
    }

    /**
     * Test get absolute "safe" path
     *
     */
    function test_getPath()
    { return;
        $this->assertEquals(
            $this->_data['basePath'] .
            $this->_data['uploadDir'] .
            '/',
            $this->_model->getPath('../tests/'.$this->_data['uploadDir'])
        );

        $this->assertFalse(
            $this->_model->getPath($this->_data['targetHidden'])
        );

        $this->assertFalse($this->_model->getPath('.'));
    }

    /**
     * Test delete file
     *
     */
    function test_delete()
    { return;
        try {
             $this->assertTrue(
                 $this->_model->delete($this->_data['uploadDir'], $this->_data['targetFile'])
             );

             $this->assertFalse(
                 $this->_model->delete($this->_data['uploadDir'], null)
             );

             $this->assertFalse(
                 $this->_model->delete(null, $this->_data['targetFile'])
             );
        } catch (Exception $e) {
             $this->fail($e->getMessage());
        }

        $this->assertFalse(
            is_file($this->_data['basePath'] . $this->_data['targetFileFull'])
        );
    }

    /**
     * Test create thumb
     *
     */
    function test_createThumb()
    {return;
        $size = 1;
        try {
            $true  = $this->_model->createThumb($this->_data['targetFileFull'], $size);
            $false = $this->_model->createThumb($this->_data['targetFileFull'], null);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    /**
     * Test get Directory entries
     *
     */
    function test_getDirectory()
    {return;
        try {
            $result  = $this->_model->getDirectory($this->_data['uploadDir']);
            $default = $this->_model->getDirectory(null);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertEquals(
            $this->_data['targetFile'],
            $result['1'][$this->_data['targetFile']]['basename']
        );

        $this->assertEquals($result, $default);
    }

    /**
     * Test upload
     *
     */
    function test_upload()
    {return;
        $_FILES['0'] = $this->_uploadFile['correct'];
        $this->_uploadSucceed();

        $options = $this->_model->getOptions();

        $this->_uploadFail(null, $options['errors']['directory']);

        $_FILES['0']['size'] = $this->_uploadFile['incorrect']['size'];
        $this->_uploadFail($this->_data['uploadDir'], $options['errors']['size']);

        $_FILES['0'] = $this->_uploadFile['correct'];
        $_FILES['0']['name'] = $this->_uploadFile['incorrect']['name'];
        $this->_uploadFail($this->_data['uploadDir'], $options['errors']['default']);

        $_FILES['0'] = $this->_uploadFile['correct'];
        $_FILES['0']['error'] = $this->_uploadFile['incorrect']['error'];
        $this->_uploadFail($this->_data['uploadDir'], $options['errors']['default'], 'asdfas');

        $_FILES['0'] = $this->_uploadFile['correct'];
        $_FILES['0']['type'] = $this->_uploadFile['incorrect']['type'];
        $this->_uploadFail($this->_data['uploadDir'], $options['errors']['filetype']);

        $_FILES['0'] = $this->_uploadFile['correct'];
        $_FILES['0']['name'] = $this->_uploadFile['incorrect']['name2'];
        $this->_uploadFail($this->_data['uploadDir'], $options['errors']['filetype']);


        //TODO test if is file upload with rename
    }

    /**
     * This must fail
     *
     */
    private function _uploadFail($directory, $message)
    {
        try {
            $result = $this->_model->upload($directory);
        } catch (Exception $e) {
            $this->assertEquals($message, $e->getMessage());
        }
    }

    /**
     * This must succeed
     *
     */
    private function _uploadSucceed()
    {
        $_FILES['0'] = $this->_uploadFile['correct'];
        try {
            $result  = $this->_model->upload($this->_data['uploadDir']);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * tear Down
     *
     */
    public function tearDown()
    {
        $this->_clean($this->_data['basePath'], $this->_data['uploadDir']);
        parent::tearDown();
    }
}

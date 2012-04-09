<?php
/**
 * Uploads images controller for pages module
 *
 * @category   Application
 * @package    Pages
 * @subpackage Controller
 */
class Pages_ImagesController extends Core_Controller_Action
{
    /**
     * Index
     */
    public function uploadAction()
    {
        // disable layouts for this action:
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost()) {
            try {
                $destination = PUBLIC_PATH . "/uploads/pages";

                /* Check destination folder */
                if (!is_dir($destination)) {
                    if (is_writable(PUBLIC_PATH . "/uploads")) {
                        mkdir($destination);
                    } else {
                        throw new Exception("Uploads directory is not writable");
                    }
                }

                /* Uploading Document File on Server */
                $upload = new Zend_File_Transfer_Adapter_Http();
                try {
                    // upload received file(s)
                    $upload->receive();
                } catch (Zend_File_Transfer_Exception $e) {
                    $e->getMessage();
                }

                // you MUST use following functions for knowing about uploaded file
                // Returns the file name for 'doc_path' named file element
                $filePath = $upload->getFileName('file');

                // Returns the mimetype for the 'file' form element
                $mimeType = $upload->getMimeType('file');

                // mimeType validation
                switch ($mimeType) {
                    case 'image/jpg':
                    case 'image/jpeg':
                    case 'image/pjpeg':
                        $ext = 'jpg';
                        break;
                    case 'image/png':
                        $ext = 'png';
                        break;
                    case 'image/gif':
                        $ext = 'gif';
                        break;
                    default:
                        throw new Exception('Wrong mimetype of uploaded file. Received "'.$mimeType.'"');
                        break;
                }

                // prepare filename
                $name = pathinfo($filePath, PATHINFO_FILENAME);
                $name = strtolower($name);
                $name = preg_replace('/[^a-z0-9_-]/', '-', $name);

                // rename uploaded file
                $renameFile = $name .'.'. $ext;
                $counter = 0;
                while (file_exists($destination .'/'. $renameFile)) {
                    $counter++;
                    $renameFile = $name .'-'. $counter .'.'. $ext;
                }

                $fullFilePath = $destination.'/'.$renameFile;

                // Rename uploaded file using Zend Framework
                $filterFileRename = new Zend_Filter_File_Rename(array('target' => $fullFilePath, 'overwrite' => true));

                $filterFileRename -> filter($filePath);

                $this->_helper->viewRenderer->setNoRender(true);
                echo "<img src='/uploads/pages/$renameFile' alt='' />";
            } catch (Exception $e) {
                $this->_forwardError($e->getMessage());
            }
        } else {
            $this->_forwardError('Internal Error of Uploads controller');
        }
    }
}
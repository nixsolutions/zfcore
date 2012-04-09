<?php
/**
 * Uploads files controller for pages module
 *
 * @category   Application
 * @package    Pages
 * @subpackage Controller
 */
class Pages_FilesController extends Core_Controller_Action
{
    /**
     * upload
     */
    public function uploadAction()
    {
        // disable layouts for this action:
        $this->_helper->layout->disableLayout();
        if ($this->_request->isPost()) {
            try {
                $destination = PUBLIC_PATH . "/uploads/files";

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

                // pathinfo
                $name = pathinfo($filePath, PATHINFO_FILENAME);
                $ext  = pathinfo($filePath, PATHINFO_EXTENSION);

                // prepare filename
                $name = strtolower($name);
                $name = preg_replace('/[^a-z0-9_-]/', '-', $name);

                // prepare extension
                if ($ext == 'php'
                    or $ext == 'php4'
                    or $ext == 'php5'
                    or $ext == 'phtml'
                    ) {
                    $ext = 'phps';
                }

                // rename uploaded file
                $renameFile = $name .'.'. $ext;
                $counter = 0;
                while (file_exists($destination .'/'. $renameFile)) {
                    $counter++;
                    $renameFile = $name .'-'. $counter .'.'. $ext;
                }

                $fileIco = $this->getIco($ext);
                $fullFilePath = $destination.'/'.$renameFile;

                // Rename uploaded file using Zend Framework
                $filterFileRename = new Zend_Filter_File_Rename(array('target' => $fullFilePath, 'overwrite' => true));

                $filterFileRename -> filter($filePath);

                $this->_helper->viewRenderer->setNoRender(true);

                echo '<a href="javascript:void(null);" rel="'.$renameFile.'" class="redactor_file_link redactor_file_ico_'.$fileIco.'" title="'.$renameFile.'">'.$renameFile.'</a>';

            } catch (Exception $e) {
                $this->_forwardError($e->getMessage());
            }
        } else {
            $this->_forwardError('Internal Error of Uploads controller');
        }
    }

    /**
     * @param $type
     * @return string
     */
    protected function getIco($type)
    {
    	$fileicons = array('other' => 0,
                           'avi' => 'avi',
                           'doc' => 'doc',
                           'docx' => 'doc',
                           'gif' => 'gif',
                           'jpg' => 'jpg',
                           'jpeg' => 'jpg',
                           'mov' => 'mov',
                           'csv' => 'csv',
                           'html' => 'html',
                           'pdf' => 'pdf',
                           'png' => 'png',
                           'ppt' => 'ppt',
                           'rar' => 'rar',
                           'rtf' => 'rtf',
                           'txt' => 'txt',
                           'xls' => 'xls',
                           'xlsx' => 'xls',
                           'zip' => 'zip');

    	if (isset($fileicons[$type])) {
            return $fileicons[$type];
        } else {
            return 'other';
        }
    }

    /**
     * download file
     */
    public function downloadAction()
    {
        // disable layout
        $this->_helper->layout->disableLayout();

        $filename = $this->_request->getParam('file');

        if (!file_exists($filename)) {
            $this->_forwardError('File not found');
        }

        // disable renderer
        $this->_helper->viewRenderer->setNoRender(true);

        $from = $to = 0;
        $cr = null;

        // support partial content
        if (isset($_SERVER['HTTP_RANGE'])) {
            $range = substr($_SERVER['HTTP_RANGE'], strpos($_SERVER['HTTP_RANGE'], '=')+1);
            $from = strtok($range, '-');
            $to = strtok('/');
            if ($to > 0) $to++;
            if ($to) $to -= $from;
            header('HTTP/1.1 206 Partial Content');
            $cr = 'Content-Range: bytes ' . $from . '-' . (($to)?($to . '/' . $to+1):filesize($filename));
        } else {
            header('HTTP/1.1 200 Ok');
        }

        // ETag support
        $etag = md5($filename);
        $etag = substr($etag, 0, 8) . '-' . substr($etag, 8, 7) . '-' . substr($etag, 15, 8);
        header('ETag: "' . $etag . '"');

        header('Accept-Ranges: bytes');
        header('Content-Length: ' . (filesize($filename)-$to+$from));
        if ($cr) {
            header($cr);
        }
        header('Connection: close');
        header('Content-Type: application/octet-stream');
        header('Last-Modified: ' . gmdate('r', filemtime($filename)));
        header('Content-Disposition: attachment; filename="' . basename($filename) . '";');

        // send file to browser
        $f = fopen($filename, 'r');
        if ($from) {
            fseek($f, $from, SEEK_SET);
        }
        if (!isset($to) || empty($to)) {
            $size = filesize($filename)-$from;
        } else {
            $size = $to;
        }
        $downloaded = 0;
        while (!feof($f) && !connection_status() && ($downloaded<$size)) {
            echo fread($f, 512000);
            $downloaded += 512000;
            flush();
        }
        fclose($f);
        exit();
    }

    /**
     * delete
     */
    public function deleteAction()
    {
        $file = $this->_request->getParam('file');
    }

}
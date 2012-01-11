<?php
/**
 * MessageController for feedback module
 *
 * @category   Application
 * @package    Dashboard
 * @subpackage Controller
 *
 * @version  $Id: MessageController.php 1564 2009-10-30 09:09:03Z secunda $
 */
class Feedback_ManagementController extends Core_Controller_Action_Crud
{
    public function init()
    {
        /* Initialize */
        parent::init();

        $this->_beforeGridFilter(array(
              '_addAllTableColumns',
              '_addReadColumn',
              '_addDeleteColumn',
              '_showFilter'
        ));
    }

    /**
     */
    public function readAction()
    {
        if (!$id = (int)$this->_getParam('id')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

        $table = new Feedback_Model_Feedback_Table();
        if (!$row = $table->getById($id)) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

        // Настроить форму чтения сообщения
        $form = $this->_getCreateForm();
        $form->setAction($this->view->url(array('action' => 'reply')));

        // get template for reply
        $mail = new Mail_Model_Templates_Table();
        $template = $mail->getModel('reply');

        $replyMail = $template->toArray();
        $replyMail['id'] = $row->id;
        $replyMail['sender'] = $row->sender;
        $replyMail['email'] = $row->email;
        $replyMail['message'] = $replyMail['bodyHtml'];
        unset($replyMail['body']);

        $form->setDefaults($replyMail);

        // change feedback status
        if (Feedback_Model_Feedback::STATUS_REPLY != $row->status) {
            $row->status = Feedback_Model_Feedback::STATUS_READ;
            $row->updated = date('Y-m-d H:i:s');
            $row->save();
        }
        $this->view->form = $form;
        $this->view->mail = $row;
    }

    /**
     */
    public function replyAction()
    {
        if (!$this->getRequest()->isPost()) {
        	$this->_helper->redirector('index');
        }

        if (!$id = (int)$this->_getParam('id')) {
            throw new Zend_Controller_Action_Exception('Param id is required');
        }

        $table = new Feedback_Model_Feedback_Table();
        if (!$row = $table->getById($id)) {
            throw new Zend_Controller_Action_Exception('Feedback not found');
        }

        $form = $this->_getCreateForm();
        $form->setAction($this->view->url());

        // Проверить, если данные пришли из indexAction
        if ($this->_getParam('viewForm')) {
            $form->setDefault('id', $id);
            $form->setDefaults($row->toArray());
        } else if ($form->isValid($this->_getAllParams())) {
            $data = $form->getValues();
            $message = $row->toArray();

            $mail = new Zend_Mail();
            // Формирование MIME данных
            $mime = null;
            if ($form->inputFile->isUploaded()) {
                $file = $form->inputFile->getFileInfo();
                $mime = Mail_Model_Mail::getMimePart(
                    array(
                         'file' => $file['inputFile']['tmp_name'],
                         'name' => $file['inputFile']['name'],
                         'description' => 'Attachment Image'
                    )
                );
                $mail->setMime($mime);
                // Удалить загруженный файл
                unlink($file['tmp_name']);
            }
            // Формирование шаблона собщения
            $subject = $data['subject'] ?
                    $data['subject'] :
                    ('To reply on "' . $message['subject'] . '"');

            $template = new Mail_Model_Templates_Model();
            $template->toName = $data['sender'];
            $template->toEmail = $data['email'];
            $template->fromName = $data['fromName'];
            $template->fromEmail = $data['fromEmail'];
            $template->subject = $subject;
            $template->bodyHtml = $data['message'];

            // if message with file, change %image% in template to link
            if ($form->inputFile->isUploaded()) {
                $image = '<img src="cid:' .
                         $mime->id .
                         '" title="' .
                         $mime->description . '"/>';

                $template->assign('image', $image);
            }
            // Посылка собщения
            $template->send($mail);
            // Если надо сохранить копию сообщения
            //XXX
            if (isset($data['saveCopy'])) {
                $login = Zend_Auth::getInstance()->getIdentity()->login;
                $table->createRow(
                    array(
                         'sender' => ($data['sender'] ? $data['sender'] : $login),
                         'subject' => $subject,
                         'message' => $data['message'],
                         'email' => ($data['email'] ? $data['email'] : 'zfc@nixsolutions.com'),
                         'status' => Feedback_Model_Feedback::STATUS_REPLY,
                         'created' => date('Y-m-d H:i:s')
                    )
                )->save();
            }

            $row->status = Feedback_Model_Feedback::STATUS_REPLY;
            $row->updated = date('Y-m-d H:i:s');
            $row->save();

            $this->_helper->flashMessenger('Successfully!');
            $this->_helper->redirector('index');
        }

        $this->view->form = $form;
        $this->_setDefaultScriptPath();
    }

    /**
     *
     * return create form for scaffolding
     *
     * @return  Zend_Form
     */
    public function _getCreateForm()
    {
        return new Feedback_Model_Feedback_Form_Reply();
    }

    /**
     *
     * return edit form for scaffolding
     *
     * @return  Zend_Form
     */
    public function _getEditForm()
    {
    }

    /**
     * get feedback table
     *
     * @return Feedback_Model_Feedback_Table
     */
    public function _getTable()
    {
        return new Feedback_Model_Feedback_Table();
    }

    /**
     * add edit column to grid
     *
     * @return void
     */
    public function _addReadColumn()
    {
        $this->grid->setColumn('read', array(
            'name' => 'Read',
            'formatter' => array($this, 'readLinkFormatter')
        ));
    }

    /**
     * edit link formatter
     *
     * @param $value
     * @param $row
     * @return string
     */
    public function readLinkFormatter($value, $row)
    {
        $link = '<a href="%s" class="read">Read</a>';
        $url = $this->getHelper('url')->url(array(
             'action' => 'read',
             'id' => $row['id']
        ), 'default');

        return sprintf($link, $url);
    }
}

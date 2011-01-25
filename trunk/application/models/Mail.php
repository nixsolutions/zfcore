<?php
/**
 * Mode Page
 *
 * @category Application
 * @package Model
 * 
 * @version  $Id: Mail.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Model_Mail
{
    /**
     * Send registration email
     *
     * @return bool
     */
    public function register($aUser)
    {
        $template = Core_Mailer::getTemplate('registration');
        $template->toEmail = $aUser->email;
        $template->toName  = $aUser->login;
        $template->assign('hash', $aUser->hashCode);
        $template->assign('host', $_SERVER['HTTP_HOST']);
        if ($template->signature) {
            $template = self::assignLayout($template);
        }
        return Core_Mailer::send($template);
    }
    
    /**
     * Send forget password email
     *
     * @return bool
     */
    public function forgetPassword($aUser)
    {
        $template = Core_Mailer::getTemplate('forgetPassword');
        $template->toEmail = $aUser->email;
        $template->toName  = $aUser->login;
        $template->assign('hash', $aUser->hashCode);
        $template->assign('host', $_SERVER['HTTP_HOST']);
        if ($template->signature) {
            $template = self::assignLayout($template);
        }
        return Core_Mailer::send($template);
    }
    
    /**
     * Send forget password Confirmation email
     *
     * @return bool
     */
    public function newPassword($aUser, $aPassword)
    {
        $template = Core_Mailer::getTemplate('newPassword');
        $template->toEmail = $aUser->email;
        $template->toName  = $aUser->login;
        if ($template->signature) {
            $template = self::assignLayout($template);
        }
        $template->assign('password', $aPassword);
        
        return Core_Mailer::send($template);
    }
    
    /**
     * Send arbitrary messages with MIME data
     * 
     * @param Core_Mailer_Template $template
     * @return bool
     */
    public function sendArbitraryMessage(Core_Mailer_Template $template)
    {
        if ($template->signature) {
            $template = self::assignLayout($template);
        }
        
        return Core_Mailer::send($template);
    }

    /**
     * @param  array $data
     * @return null|Zend_Mime_Part
     */
    public function getMimePart($data)
    {
        if (is_array($data)) {
            $mime = new Zend_Mime_Part(file_get_contents($data['file']));
            // Указываем тип содержимого файла 
            $mime->type = isset($data['type']) ? $data['type'] : 'application/octet-stream';  
            $mime->disposition = Zend_Mime::DISPOSITION_INLINE;  
            // Каким способом закодировать файл в письме 
            $mime->encoding = Zend_Mime::ENCODING_BASE64;  
            // Название файла в письме  
            $mime->filaname = $data['name'];  
            // пдентификатор содержимого. 
            // По нему можно обращаться к файлу в теле письма 
            $mime->id = md5(time());  
            // Описание вложеного файла 
            $mime->description = $data['description'];
            
            return $mime;
        }
        
        return null;
    }
    
    /**
     * Get Layout
     *
     */
    public function getLayout()
    {
        return Model_Option::get('signature');
    }
    
    /**
     * Set Layout
     * 
     * @param string $value
     * @return object Model_Option
     */
    public function setLayout($value)
    {
        return Model_Option::set('signature', $value);
    }
    
    /**
     * Assign Layout
     * 
     * @param  Core_Mailer_Template $template
     * @return Core_Mailer_Template
     */
    public static function assignLayout(Core_Mailer_Template $template)
    {
        if ($layout = self::getLayout()) {
            $template->body = str_replace('%body%', $template->body, $layout);
        }
        return $template;
    }
    
    /**
     * Send mail
     *
     * @param array $aParams
     * @return void
     * @todo new Model
     */
    public function send($aParams)
    {
        $template = new Core_Mailer_Template($aParams);
        
        $user = new Model_User_Manager();
        $users = $user->getFilter($aParams);
        $errors = array();
        
        foreach ($users as $user) {
            $template->toName  = $user['login'];
            $template->toEmail = $user['email'];
            if ($template->signature) {
                $template = self::assignLayout($template);
            }
            try {
               Core_Mailer::send($template);
            } catch (Exception $e) {
                $errors[] = 'Unable send mail to '.$user['email'];
            }
        }
        
        if (in_array(true, $errors)) {
            throw new Exception(join('<br />,', $errors));
        }
    }
}
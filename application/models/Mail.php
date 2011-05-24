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
    public static function register($aUser)
    {
        $table = new Mail_Model_Templates_Table();
        $template = $table->getModel('registration');
        $template->toEmail = $aUser->email;
        $template->toName  = $aUser->login;
        $template->assign('hash', $aUser->hashCode);
        $template->assign('host', $_SERVER['HTTP_HOST']);

        if ($template->signature) {
            self::assignLayout($template);
        }

        return $template->send();
    }

    /**
     * Send forget password email
     *
     * @return bool
     */
    public static function forgetPassword($aUser)
    {
        $table = new Mail_Model_Templates_Table();
        $template = $table->getModel('forgetPassword');
        $template->toEmail = $aUser->email;
        $template->toName  = $aUser->login;
        $template->assign('hash', $aUser->hashCode);
        $template->assign('host', $_SERVER['HTTP_HOST']);

        if ($template->signature) {
            self::assignLayout($template);
        }

        return $template->send();
    }

    /**
     * Send forget password Confirmation email
     *
     * @return bool
     */
    public static function newPassword($aUser, $aPassword)
    {
        $table = new Mail_Model_Templates_Table();
        $template = $table->getModel('newPassword');
        $template->toEmail = $aUser->email;
        $template->toName  = $aUser->login;

        $template->assign('password', $aPassword);

        if ($template->signature) {
            self::assignLayout($template);
        }
        return $template->send();
    }

    /**
     * Get mime part
     *
     * @param  array $data
     * @return null|Zend_Mime_Part
     */
    public static function getMimePart($data)
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
    public static function getLayout()
    {
        return Model_Option::get('signature');
    }

    /**
     * Set Layout
     *
     * @param string $value
     * @return object Model_Option
     */
    public static function setLayout($value)
    {
        return Model_Option::set('signature', $value);
    }

    /**
     * Assign Layout
     *
     * @param  Mail_Model_Templates_Model $template
     */
    public static function assignLayout(Mail_Model_Templates_Model $template)
    {
        if ($layout = self::getLayout()) {
            $template->bodyHtml = str_replace(
                '%body%',
                $template->bodyHtml,
                $layout
            );
        }
    }
}
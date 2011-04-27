<?php
/**
 * This is the DbTable class for the mail table.
 *
 * @category Application
 * @package Model
 * @subpackage DbTable
 *
 * @version  $Id: Manager.php 47 2010-02-12 13:17:34Z AntonShevchuk $
 */
class Mail_Model_Templates_Model
{
    /**
     * @var string
     */
    protected static $_layout;

    /**
     * Default values for mail template
     *
     * @var array
     */
    protected $_data = array(
        'toEmail'      => null,
        'toName'       => null,
        'subject'      => null,
        'bodyHtml'     => null,
        'bodyText'     => null,
        'enableLayout' => null
    );

    /**
     * Constructor;
     * Sets a values to default properties
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->setFromArray($data);
    }

    /**
     * Set from array
     *
     * @param  array $data
     * @return self
     */
    public function setFromArray(array $data)
    {
        $this->_data = array_merge($this->_data, $data);
        return $this;
    }

    /**
     * Set layout
     */
    public static function setLayout($layout)
    {
        self::$_layout = (string) $layout;
    }

    /**
     * Assign data to template
     *
     * @param string $name
     * @param string $value
     * @return self
     */
    public function assign($name, $value)
    {
        $this->_data = str_replace("%" . $name . "%", $value, $this->_data);
        return $this;
    }

    /**
     * Get property
     *
     * @param string $key
     * @return string|null
     */
    public function __get($key)
    {
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        }
        return null;
    }

    /**
     * Set property
     *
     * @param string $key
     * @param string $value
     */
    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->_data)) {
            $this->_data[$key] = $value;
        }
    }

    /**
     * Send email
     *
     * @param Zend_Mail $mail
     * @return Zend_Mail
     */
    public function send(Zend_Mail $mail = null)
    {
        if ($mail) {
            $mail = clone $mail;
        } else {
            $mail = new Zend_Mail();
        }
        $mail = $this->populate($mail);

        return $mail->send();
    }

    /**
     * Populate mail instance
     *
     * @param Zend_Mail $mail
     * @return Zend_Mail
     */
    public function populate(Zend_Mail $mail)
    {
        if ($this->fromEmail || $this->fromName) {
            $mail->setFrom($this->fromEmail, $this->fromName);
        }
        if ($this->subject) {
            $mail->setSubject($this->subject);
        }
        if ($this->bodyHtml) {
            $html = $this->bodyHtml;
            if ($this->enableLayout && self::$_layout) {
                $html = str_replace('%body%', $html, self::$_layout);
            }
            $mail->setBodyHtml($html);
        }
        if ($this->bodyText) {
            $mail->setBodyText($this->bodyText);
        }

        return $mail;
    }
}

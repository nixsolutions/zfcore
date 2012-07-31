<?php

/**
 * User entity Model
 *
 * @todo http://www.zimuel.it/blog/?p=86
 * @category Application
 * @package Model
 */
class Users_Model_User extends Core_Db_Table_Row_Abstract
{
    const STATUS_REGISTER = 'registered';
    const STATUS_ACTIVE   = 'active';
    const STATUS_BLOCKED  = 'blocked';
    const STATUS_REMOVED  = 'removed';

    const ROLE_GUEST = 'guest';
    const ROLE_USER  = 'user';
    const ROLE_ADMIN = 'admin';

    /**
     * Minimum of username length
     * @var integer
     */
    const MIN_USERNAME_LENGTH = 3;

    /**
     * Maximum of username length
     * @var integer
     */
    const MAX_USERNAME_LENGTH = 32;

    /**
     * Minimum of password length
     * @var integer
     */
    const MIN_PASSWORD_LENGTH = 6;

    /**
     * Maximux of firstname length
     * @var integer
     */
    const MAX_FIRSTNAME_LENGTH = 255;
    /**
     * Maximux of Lastname length
     * @var integer
     */
    const MAX_LASTNAME_LENGTH = 255;

    /**
     * Get user name
     *
     * @return string
     */
    public function getName()
    {
        if ($this->lastname || $this->firstname) {
            return $this->firstname . ' ' . $this->lastname;
        }
        return $this->login;
    }

    /**
     * Is password
     *
     * @param string $value Password value
     *
     * @return boolean
     */
    public function isPassword($value)
    {
        return $this->encrypt($value) == $this->_data['password'];
    }

    /**
     * Is column value equal to
     *
     * @param string $column Column Name
     * @param string $value  Value of column
     *
     * @return boolean
     */
    public function is($column, $value)
    {
        return $this->{$column} == $value;
    }

    /**
     * Encrypt password
     *
     * @param string $password Password value
     *
     * @return string
     */
    public function encrypt($password)
    {
        return md5($this->salt . $password);
    }

    /**
     * Set row field value
     *
     * @param string $columnName The column key.
     * @param mixed  $value      The value for the property.
     *
     * @return void
     */
    public function __set($columnName, $value)
    {
        switch ($columnName) {
            case 'ip':
                $value = ip2long($value);
                break;
            case 'avatar':
                if (!$value) {
                    return;
                }
                if (strpos($value, 'http') !== 0 && strpos($value, '/') !== 0) {
                    $value = '/uploads/' . $value;
                }
                break;
            case 'password':
                if (!$value) {
                    return;
                }
                if (!$this->salt) {
                    parent::__set('salt', md5(uniqid()));
                }
                $value = $this->encrypt($value);
                break;
        }
        parent::__set($columnName, $value);
    }

    /**
     * @see Zend_Db_Table_Row_Abstract::__call()
     *
     * @param $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, array $args)
    {
        if (strpos($method, 'is') === 0) {
            $method = substr($method, 2);
            $method{0} = strtolower($method{0});

            array_unshift($args, $method);

            return call_user_func_array(array($this, 'is'), $args);
        }
        parent::__call($method, $args);
    }


    /**
     * Get row field value
     *
     * @param string $columnName Column Name
     *
     * @return string
     */
    public function __get($columnName)
    {
        switch ($columnName) {
            case 'password':
                //return;
                break;
            case 'ip':
                $result = long2ip(parent::__get($columnName));
                break;
            default:
                $result = parent::__get($columnName);
                break;
        }
        return $result;
    }

    /**
     * Changed parent method
     * if $clean == false it return raw data(as is in base)
     * always use $clean true to get data if you can
     *
     * @param bool $clean Flag for cleaning raw data
     *
     * @return array
     */
    public function toArray($clean = false)
    {
        if ($clean) {
            $result = array();
            foreach ($this->_data as $key => $value) {
                $result[$key] = $this->{$key};
            }
            return $result;
        }
        return parent::toArray();
    }

    /**
     * Login user
     *
     * @param boolean $update Update count and logined data flag
     *
     * @return Users_Model_User
     */
    public function login($update = true)
    {
        if ($update) {
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $this->logined = date('Y-m-d H:i:s');
            $this->ip = $request->getClientIp();
            $this->count++;
            $this->save();
        }

        Zend_Auth::getInstance()->getStorage()->write($this);
        return $this;
    }

    /**
     * Logout user
     *
     * @return Users_Model_User
     */
    public function logout()
    {
        Zend_Auth::getInstance()->clearIdentity();

        return $this;
    }

    /**
     * Allows pre-update logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function _update()
    {
        $this->updated = date('Y-m-d H:i:s');
    }

    /**
     * Allows pre-insert logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return  void
     */
    protected function _insert()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->ip = $request->getClientIp();

        $this->created = date('Y-m-d H:i:s');
        $this->_update();
    }
}
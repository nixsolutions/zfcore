<?php 
/**
 * Mode Page
 *
 * @todo http://www.zimuel.it/blog/?p=86
 * @category Application
 * @package Model
 * 
 * @version  $Id: User.php 146 2010-07-05 14:22:20Z AntonShevchuk $
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
     * Set row field value
     *
     * @param  string $columnName The column key.
     * @param  mixed  $value      The value for the property.
     * @return void
     */
    public function __set($columnName, $value)
    {
        switch ($columnName) {
            case 'ip':
                $value = ip2long($value);
                break;
            case 'password':
                if (!$this->salt) {
                    parent::__set('salt', md5(uniqid()));
                }
                $value = md5($this->salt . $value);
                break;
        }
        parent::__set($columnName, $value);
    }

    /**
     * Get row field value
     *
     * @param  string $columnName The column key.
     * @return string
     */
    public function __get($columnName)
    {
        switch ($columnName) {
            case 'password':
                return;
                break;
            case 'ip':
                return long2ip(parent::__get($columnName));
                break;
        }
        return parent::__get($columnName);
    }
    
    /**
     * Changed parent method
     * if $clean == false it return raw data(as is in base)
     * always use $clean true to get data if you can
     * 
     * @param bool $clean
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
        $this->created = date('Y-m-d H:i:s');
        $this->_update();
    }
}
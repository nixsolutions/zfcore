<?php
/**
 * Class Options_Model_Options_Manager
 *
 * for options table management
 *
 * @category Application
 * @package  Model
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 *
 * @version  $Id: Option.php 207 2010-10-20 15:37:18Z AntonShevchuk $
 */
class Options_Model_Options_Row extends Zend_Db_Table_Row_Abstract
{
    const TYPE_INT    = 'int';
    const TYPE_FLOAT  = 'float';
    const TYPE_STRING = 'string';
    const TYPE_ARRAY  = 'array';
    const TYPE_OBJECT = 'object';

    /**
     * Get option value
     *
     * @return mixed
     */
    public function getValue()
    {
        switch ($this->type) {
            case self::TYPE_INT:
                return (int) $this->value;
                break;
            case self::TYPE_FLOAT:
                return (float) $this->value;
                break;
            case self::TYPE_ARRAY:
            case self::TYPE_OBJECT:
                return unserialize($this->value);
                break;
            default:
                return $this->value;
        }
    }
    /**
     * Allows pre-insert logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function _insert()
    {
        $this->_update();
    }

    /**
     * Allows pre-update logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function _update()
    {
        if (!$this->type) {
            if (is_object($this->value)) {
                $this->type = self::TYPE_OBJECT;
            } elseif (is_array($this->value)) {
                $this->type = self::TYPE_ARRAY;
            } elseif (is_bool($value) || is_integer($value)) {
                $this->type = self::TYPE_INT;
            } elseif (is_float($value)) {
                $this->type = self::TYPE_FLOAT;
            } else {
                $this->type = self::TYPE_STRING;
            }
        }

        if (self::TYPE_OBJECT == $this->type
            || self::TYPE_ARRAY == $this->type) {
            $this->value = serialize($this->value);
        }
    }
}

<?php
/**
 * Model CommentAlias
 *
 * @category Application
 * @package Comments
 * @subpackage Model
 * 
 * @version  $Id: CommentAlias.php 2011-11-21 11:59:34Z pavel.machekhin $
 */
class Comments_Model_CommentAlias extends Core_Db_Table_Row_Abstract
{
    /**
     * Magic method to set some row fields
     *
     * @return  void
     */
    public function _insert()
    {
        $this->created = date("Y-m-d h:i:s");
        
        $this->_update();
    }

    /**
     * Magic method to update some row fields
     *
     * @return void
     */
    public function _update()
    {
        $this->options = Zend_Json_Encoder::encode($this->options);
        $this->updated = date("Y-m-d h:i:s");
    }
    
    /**
     * Get the options
     * 
     * @return array
     */
    public function getOptions()
    {
        return Zend_Json_Decoder::decode($this->options);
    }
    
    /**
     * Check if "keyRequired" option is enabled
     * 
     * @return boolean
     */
    public function isKeyRequired()
    {
        return in_array('keyRequired', $this->getOptions());
    }
    
    /**
     * Check if "titleDisplayed" option is enabled
     * 
     * @return boolean
     */
    public function isTitleDisplayed()
    {
        return in_array('titleDisplayed', $this->getOptions());
    }
    
    /**
     * Check if "paginatorEnabled" option is enabled
     * 
     * @return boolean
     */
    public function isPaginatorEnabled()
    {
        return in_array('paginatorEnabled', $this->getOptions());
    }
    
    /**
     * Check if "preModerationRequired" option is enabled
     * 
     * @return boolean
     */    
    public function isPreModerationRequired()
    {
        return in_array('preModerationRequired', $this->getOptions());
    }
}
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
     * Allows pre-insert logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return  void
     */
    public function _insert()
    {
        $this->created = date("Y-m-d h:i:s");
        
        $this->_update();
    }

    /**
     * Allows pre-update logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    public function _update()
    {
        $this->options = Zend_Json_Encoder::encode($this->options);
        $this->updated = date("Y-m-d h:i:s");
    }
    
    public function getOptions()
    {
        return Zend_Json_Decoder::decode($this->options);
    }
    
    public function isKeyRequired()
    {
        return in_array('keyRequired', $this->getOptions());
    }
    
    public function isTitleDisplayed()
    {
        return in_array('titleDisplayed', $this->getOptions());
    }
    
    public function isPaginatorEnabled()
    {
        return in_array('paginatorEnabled', $this->getOptions());
    }
    
    public function isPreModerationRequired()
    {
        return in_array('preModerationRequired', $this->getOptions());
    }
}
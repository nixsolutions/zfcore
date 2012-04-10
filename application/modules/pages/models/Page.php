<?php
/**
 * Mode Page
 *
 * @category Application
 * @package Model
 * 
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 * 
 * @version  $Id: Page.php 47 2010-02-12 13:17:34Z AntonShevchuk $
 */
class Pages_Model_Page extends Core_Db_Table_Row_Abstract
{
    /** statuses */
    const STATUS_ACTIVE  = 'active';
    const STATUS_CLOSED  = 'closed';
    const STATUS_DELETED = 'deleted';

    /**
     * Allows pre-insert logic to be applied to row.
     * Subclasses may override this method. 
     *
     * @return  void
     */
    public function _insert()
    {
        $this->created = date("Y-m-d H:i:s");
    }
    
    /**
     * Allows pre-update logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    public function _update()
    {
        $this->updated = date("Y-m-d H:i:s");
    }
}

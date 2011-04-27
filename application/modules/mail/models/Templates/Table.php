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
class Mail_Model_Templates_Table extends Core_Db_Table_Abstract
{
    /**
     * @var string
     */
    protected $_name    = 'mail_templates';

    /**
     * Get template model
     *
     * @param string $alias
     * @return Mail_Model_Templates_Model
     */
    public function getModel($alias)
    {
        $select = $this->select()->where('alias = ?', $alias);
        if (!$row = $this->fetchRow($select)) {
            throw new Exception("Template by name '{$alias}' not faound");
        }
        return new Mail_Model_Templates_Model($row->toArray());
    }
}
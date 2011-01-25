<?php
/**
 * Model for getting XML dump of database
 */
class Sync_Model_Manager
{
    /**
     * Method return items from selected table
     */
    public static function getData($table, $updated, $dateField = 'updated')
    {
        try {
            $dbAdapter = Zend_Db_Table::getDefaultAdapter();

            $select = $dbAdapter->select()->from($table);
            if ($dateField != "") {
                $select->where(
                    "UNIX_TIMESTAMP(" . $dateField . ") > ?", $updated
                );
            }
       
            $result = $dbAdapter->query($select)->fetchAll();
        } catch (Exception $e) {
            return false;
        }

        return $result;
    }
}
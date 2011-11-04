<?php
/**
 *
 * @category Debud
 * @package Model
 * @subpackage Session
 *
 * @author Anna Pavlova <pavlova.anna@nixsolutions.com>
 *
 * @version  $Id$
 */
class Debug_Model_Session_Manager extends Core_Model_Manager
{
    /**
     * delete sesion item
     *
     * @param string $id
     * @return null
     */
    public function deleteSession($id = null)
    {
        unset($_SESSION[$id]);
        return true;
    }
    /**
     * createSessionArray
     *
     * create array from all Session data
     *
     * @param int $start, int $count,string $sort,string $field,string $filter
     * @return array
     *
     */
    public function createSessionArray()
    {
        $data = array();
        foreach ($_SESSION as $name => $value) {
            $data[] = array(
                'id' => $name,
                'value' => print_r($value, 1)
            );
        }
        return $data;
    }

    /**
     * create new Session
     *
     * @param array $data
     * @return object Debug_Model_Session_Manager
     *
     */
    public function createSession($data = array())
    {
        if (in_array(substr($data['value'], 0, 1), array('{', '['))) {
            if ($value = json_decode($data['value'])) {
                $data['value'] = $value;
            }
        }

        $_SESSION[$data['key']] = $data['value'];
        return $this;
    }
}
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
    private $_sessionCols     = array('id', 'namespace', 'key', 'value');

    private $_defaultOrderCol = 'namespace';

    const FIELD_DELIMITER = '@';

    /**
     * delete sesion item
     *
     * @param string $id
     * @return null
     */
    public function deleteSession($id = null)
    {
        if (is_null($id)) {
            return false;
        }
        $idArray = explode(self::FIELD_DELIMITER, $id);
        if (empty($idArray[0]) || empty($idArray[1])) {
            return false;
        }

        $namespace = new Zend_Session_Namespace($idArray[1]);
        unset($namespace->$idArray[0]);
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
    public function createSessionArray($start = 0,
                                       $count = 15,
                                       $sort = false,
                                       $field = false,
                                       $filter = false)
    {
        $flagFilter = false;
        $desc        = true;
        $sessions    = array();
        $tempArr     = array();
        $orderCol    = '';
        
        // sort data
        //   field  - ASC
        //   -field - DESC
        if ($sort
            && ltrim($sort, '-')
            && in_array(ltrim($sort, '-'), $this->_sessionCols)
            ) {
            if (strpos($sort, '-') === 0) {
                $orderCol = ltrim($sort, '-');
            } else {
                $orderCol = $sort;
                $desc = false;
            }
        }

        // Use  filter
        if ($field
            && in_array($field, $this->_sessionCols)
            && $filter
            && $filter != '*') {
            $flagFilter = true;
            $filter = str_replace('*', '(.*)', '/'. $filter .'/');
        }

        foreach (Zend_Session::getIterator() as $space) {
            $namespace = new Zend_Session_Namespace($space);
            foreach ($namespace as $index => $value) {
                if (is_array($value) || is_object($value)) {
                    $value = json_encode($value);
                }

                $lineArr = array(
                              'id'        => $index .
                                             self::FIELD_DELIMITER .
                                             $space,
                              'key'       => $index,
                              'value'     => $value,
                              'namespace' => $space,
                           );

                if ($flagFilter) {
                    if (preg_match($filter, $lineArr[$field])) {
                        if ($orderCol != '') {
                            $tempArr[$lineArr[$orderCol] . $lineArr['id']]
                                = $lineArr;
                        } else {
                            $tempArr[$lineArr[$this->_defaultOrderCol] .
                                     $lineArr['id']] = $lineArr;
                        }
                    }
                } else {
                    if ($orderCol != '') {
                        $tempArr[$lineArr[$orderCol] . $lineArr['id']]
                            = $lineArr;
                    } else {
                        $tempArr[$lineArr[$this->_defaultOrderCol] .
                                 $lineArr['id']] = $lineArr;
                    }
                }

                unset($lineArr);
            }
        }

        if ($desc) {
            ksort($tempArr);
        } else {
            krsort($tempArr);
        }

        $total = count($tempArr);
        $i = 0;
        foreach ($tempArr as $key =>$line) {
            if ($i >= $start && $i < $start+$count) {
                $sessions[$key] = $line;
            }
            $i ++;
        }
        unset($tempArr);
        
        return array('arr' => $sessions, 'total' => $total);
    }
    
    /**
     * create array from selected Session data for Edit Form
     *
     * @param string $id
     * @return array
     */
    public function createSessionFormArray($id = null)
    {
        $session = array();
        if (!is_null($id)) {
            $idArray = explode(self::FIELD_DELIMITER, $id);
            if (empty($idArray[0]) || empty($idArray[1])) {
                return null;
            }
                        
            $namespace = new Zend_Session_Namespace($idArray[1]);
            $session = array(
                           "key"       => $idArray[0],
                           "value"     => $namespace->$idArray[0],
                           "namespace" => $idArray[1] 
                       );
            if (is_array($session['value']) || is_object($session['value'])) {
                $session['value'] = json_encode($session['value']);
            }
        }
        return $session;
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
        $namespace = new Zend_Session_Namespace($data['namespace']);
        $namespace->$data['key'] = $data['value'];
        return $this;
    }
    /**
     * edit Session
     *
     * @param string $id, array $data
     * @return object Debug_Model_Session_Manager
     *
     */
    public function editSession($id = null, $data = array())
    {
        if (!is_null($id)) {
            $idArray = explode(self::FIELD_DELIMITER, $id);
            if (empty($idArray[0]) || empty($idArray[1])) {
                return null;
            }
                          
            $key   = $idArray[0];
            $space = $idArray[1];
            $this->deleteSession($id);
            $this->createSession($data);
        }
        return $this;
    }
}
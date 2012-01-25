<?php
/**
 * Created by glide/malinovskiy.
 * Date: 24.01.12
 * Time: 15:17
 */
class Core_Dump_Manager
{

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * Variable contents options
     *
     * @var array
     */
    protected $_options = array(
        // Path to project directory
        'projectDirectoryPath' => null,
        // Path to modules directory
        'modulesDirectoryPath' => null,
        // Migrations directory name
        'dumpsDirectoryName' => 'dumps',
    );

    public function __construct($options=null)
    {
        $this->_db = Zend_Db_Table::getDefaultAdapter();

        if ($options) {
            $this->_options = array_merge($this->_options, $options);
        }

    }

    /**
     * Method create dump of database
     *
     * @param null $module
     * @param string $name
     * @param string $whitelist
     * @param string $blacklist
     * @return string
     * @throws Zend_Exception
     */

    public function create($module=null, $name='', $whitelist="", $blacklist="")
    {

        $database = new Core_Db_Database(
            $this->getOptions($whitelist, $blacklist)
        );

        if ($dump = $database->getDump()) {

            $path = $this->getDumpsDirectoryPath($module);

            if(!$name) {
                list($sec, $msec) = explode(".", microtime(true));
                $name = date('Ymd_His_') . substr($msec, 0, 2).'.sql';
            }

            file_put_contents($path.DIRECTORY_SEPARATOR.$name,$dump);

            return $name;

        } else {
            throw new Zend_Exception("Can not get database dump!");
        }

    }

    /**
     * import dump in database
     * @param $name
     * @param null $module
     * @throws Zend_Exception
     */

    public function import($name, $module=null)
    {
        $path = $this->getDumpsDirectoryPath($module);

        if(file_exists($path.DIRECTORY_SEPARATOR.$name)) {

            $dump = file_get_contents($path.DIRECTORY_SEPARATOR.$name);

            return $this->_db->query($dump);


        } else {
            throw new Zend_Exception("Dump file not found!");
        }


    }

    /**
     * get options for DB
     * @param string $whitelist
     * @param string $blacklist
     * @return array
     */
    protected function getOptions($whitelist="", $blacklist="")
    {
        $strToArray = function ($str)
        {
            if(!empty($str)){

                if (strpos($str, ',')) {
                    return explode(',',$str);
                }
                return array($str);
            } else {
                return array();
            }
        };

        $blkListedTables = array();
        $blkListedTables =array_merge($blkListedTables, $strToArray($blacklist));

        $whtListedTables = array();
        $whtListedTables = array_merge($whtListedTables, $strToArray($whitelist));

        $options = array();

        if (sizeof($whtListedTables) > 0) {
            $options['blacklist'] = $blkListedTables;
        }

        if (sizeof($whtListedTables) > 0) {
            $options['whitelist'] = $whtListedTables;
        }

        $options['loaddata'] = true;

        return $options;
    }


    /**
     * Method returns path to migrations directory
     *
     * @param string $module Module name
     * @return string
     */
    public function getDumpsDirectoryPath($module = null)
    {
        if (null == $module) {
            $path = $this->getProjectDirectoryPath();
            $path .= '/' . $this->getDumpsDirectoryName();
        } else {
            $modulePath = $this->getModulesDirectoryPath() . '/' . $module;

            if (!file_exists($modulePath))
                throw new Zend_Exception('Module `' . $module . '` not exists.');

            $path = $modulePath . '/' . $this->getDumpsDirectoryName();
        }

        $this->_preparePath($path);

        return $path;
    }

    /**
     * Method prepare path (create not existing dirs)
     *
     * @param string $path
     */
    protected function _preparePath($path)
    {
        if (!is_dir($path)) {
            $this->_preparePath(dirname($path));
            mkdir($path, 0777);
        }
    }

    /**
     * Method return application directory path
     *
     * @return string
     */
    public function getProjectDirectoryPath()
    {
        if (null == $this->_options['projectDirectoryPath']) {
            throw new Zend_Exception('Project directory path undefined.');
        }

        return $this->_options['projectDirectoryPath'];
    }

    /**
     * Method return application directory path
     *
     * @return string
     */
    public function getDumpsDirectoryName()
    {
        if (null == $this->_options['dumpsDirectoryName']) {
            throw new Zend_Exception('Dumps directory name undefined.');
        }

        return $this->_options['dumpsDirectoryName'];
    }

    /**
     * Method return application directory path
     *
     * @return string
     */
    public function getModulesDirectoryPath()
    {
        if (null == $this->_options['modulesDirectoryPath']) {
            throw new Zend_Exception('Modules directory path undefined.');
        }

        return $this->_options['modulesDirectoryPath'];
    }
}

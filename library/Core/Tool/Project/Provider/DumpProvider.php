<?php
/**
 * Created by glide/malinovskiy.
 * Date: 24.01.12
 * Time: 15:14
 */

require_once 'Core/Dump/Manager.php';
require_once 'Core/Tool/Project/Provider/Abstract.php';
require_once 'Zend/Tool/Framework/Provider/Pretendable.php';

class Core_Tool_Project_Provider_DumpProvider
    extends Core_Tool_Project_Provider_Abstract
    implements Zend_Tool_Framework_Provider_Pretendable
{

    /**
     * @var Core_Dump_Manager
     */
    protected $_manager;

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return 'Dump';
    }

    protected function getManager() //move to abstract class
    {
        if (null == $this->_manager) {
            $profile = $this->_loadProfile(self::NO_PROFILE_THROW_EXCEPTION);

            $options = array(
                'projectDirectoryPath'
                => self::_getProjectDirectoryPath($profile),
                'modulesDirectoryPath'
                => self::_getModulesDirectoryPath($profile),
                'dumpsDirectoryName'
                => 'dumps',
            );

            $this->_manager = new Core_Dump_Manager($options);
        }

        return $this->_manager;
    }

    /**
     * Method create dump of database
     * @param null $module
     * @param string $name
     * @param string $whitelist
     * @param string $blacklist
     */
    public function create($module = null, $name = '', $whitelist = "", $blacklist = "")
    {
        require_once 'bootstrap.php';


        $manager = $this->getManager();

        $result = $manager->create($module, $name, $whitelist, $blacklist);

        if ($result) {
            echo 'Database dump ' . $result . ' created!';
        }

    }

    /**
     * import dump in database
     * @param $name
     * @param null $module
     */

    public function import($name, $module = null)
    {
        require_once 'bootstrap.php';

        $manager = $this->getManager();

        $result = $manager->import($name, $module);

        if ($result) {
            echo 'Database dump ' . $name . ' imported!';
        }

    }

}

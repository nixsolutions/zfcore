<?php

/**
 * Core_Asset
 *
 * asset manager
 */
class Core_Asset
{
    /**
     * @var array
     */
    protected $_includes = array();

    /**
     * @var array
     */
    protected $_excludes = array();

    /**
     * @var string
     */
    protected $_buildDir = null;

    /**
     * @var array
     */
    protected $_files = null;

    /**
     * @var array
     */
    protected $_javascripts = null;

    /**
     * @var array
     */
    protected $_stylesheets = null;

    /**
     * @var string
     */
    protected $_javascriptBuild = null;

    /**
     * @var string
     */
    protected $_stylesheetBuild = null;

    /**
     * @var Core_Asset_Adapter_Abstract
     */
    protected $_adapter = null;

    /**
     * constructor
     *
     * @param array $options
     */
    public function __construct(array $options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * set options
     *
     * @param array $options
     * @return Core_Asset
     */
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * set include
     *
     * @param $include
     * @param bool $append
     * @return Core_Asset
     */
    public function setInclude($include, $append = false)
    {
        if ($append) {
            $this->_includes = array_merge($this->_includes, (array) $include);
        } else {
            $this->_includes = array_merge((array) $include, $this->_includes);
        }
        return $this;
    }

    /**
     * get include
     *
     * @return array
     */
    public function getInclude()
    {
        return $this->_includes;
    }

    /**
     * set extend
     *
     * @param $assets
     * @return Core_Asset
     */
    public function setExtend($assets)
    {
        $assets = array_reverse((array) $assets);
        foreach ($assets as $asset) {
            $this->setInclude($asset->getInclude(), true);
        }

        foreach ($assets as $asset) {
            $this->setExclude($asset->getExclude());
        }

        return $this;
    }

    /**
     * set exclude
     *
     * @param $exclude
     * @return Core_Asset
     */
    public function setExclude($exclude)
    {
        $this->_excludes = array_merge($this->_excludes, (array) $exclude);
        return $this;
    }

    /**
     * get exclude
     *
     * @return array
     */
    public function getExclude()
    {
        return array_merge($this->_excludes, (array) $this->getBuildDir());
    }

    /**
     * set build dir
     *
     * @param $dir
     * @return Core_Asset
     */
    public function setBuildDir($dir)
    {
        $this->_buildDir = $dir;
        return $this;
    }

    /**
     * get build dir
     *
     * @return string
     */
    public function getBuildDir()
    {
        if (null === $this->_buildDir) {
            throw new Core_Exception('Build dir is not set');
        }

        return $this->_buildDir;
    }

    /**
     * set adapter
     *
     * @param $adapter
     * @return Core_Asset
     */
    public function setAdapter($adapter)
    {
        if (is_string($adapter)) {
            $class = $adapter;
            $adapter = new $class();
        } elseif (is_array($adapter)) {
            $options = $adapter;
            if (empty($options['class'])) {
                throw new Core_Exception('Adapter class is not set');
            }
            $class = $options['class'];
            unset($options['class']);

            $adapter = new $class($options);
        }

        if (!$adapter instanceof Core_Asset_Adapter_Abstract) {
            throw new Core_Exception('Adapter is not instance of Core_Asset_Adapter_Abstract');
        }

        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * get adapter
     *
     * @throws Core_Exception
     * @return Core_Asset_Adapter_Abstract
     */
    public function getAdapter()
    {
        if (null === $this->_adapter) {
            throw new Core_Exception('Adapter is not set');
        }

        return $this->_adapter;
    }

    /**
     * build javascript files
     *
     * @return void
     */
    public function buildJavascripts()
    {
        $this->_checkBuildFile($this->getJavascriptBuild());

        $this->getAdapter()->buildJavascripts(
            array_reverse($this->getJavascripts()),
            $this->getJavascriptBuild()
        );
    }

    /**
     * build stylesheet files
     *
     * @return void
     */
    public function buildStylesheets()
    {
        $this->_checkBuildFile($this->getStylesheetBuild());

        $this->getAdapter()->buildStylesheets(
            array_reverse($this->getStylesheets()),
            $this->getStylesheetBuild()
        );
    }

    /**
     * get javascript files
     *
     * @return array
     */
    public function getJavascripts()
    {
        if (null === $this->_javascripts) {
            $files = array();
            foreach($this->_getFiles() as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if ($ext === 'js') {
                    $files[] = $file;
                }
            }
            $this->_javascripts = $files;
        }
        return $this->_javascripts;
    }

    /**
     * get stylesheet files
     *
     * @return array|null
     */
    public function getStylesheets()
    {
        if (null === $this->_stylesheets) {
            $files = array();
            foreach($this->_getFiles() as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if ($ext === 'css') {
                    $files[] = $file;
                }
            }
            $this->_stylesheets = $files;
        }
        return $this->_stylesheets;
    }

    /**
     * get javascript build file
     *
     * @throws Core_Exception
     * @return string
     */
    public function getJavascriptBuild()
    {
        if (null === $this->_javascriptBuild) {
            $this->_javascriptBuild = rtrim($this->getBuildDir(), '/') . '/'
                                    . 'build-' . Core_Version::getVersion() . '.js';
        }
        return $this->_javascriptBuild;
    }

    /**
     * get stylesheet build files
     *
     * @throws Core_Exception
     * @return string
     */
    public function getStylesheetBuild()
    {
        if (null === $this->_stylesheetBuild) {
            $this->_stylesheetBuild = rtrim($this->getBuildDir(), '/') . '/'
                                    . 'build-' . Core_Version::getVersion() . '.css';
        }
        return $this->_stylesheetBuild;
    }

    /**
     * get files
     *
     * @throws Core_Exception
     * @return array
     */
    protected function _getFiles()
    {
        if (null === $this->_files) {
            if (!$this->_includes) {
                throw new Core_Exception('Include list is empty');
            }

            $this->_files = array();
            foreach ($this->_includes as $include) {
                if (is_file($include)) {
                    $this->_files[] = $include;
                } elseif (is_dir($include)) {
                    $this->_files = array_merge($this->_files, self::recursiveScanDir($include));
                } else {
                    throw new Core_Exception('Cannot get access to "' . $include . '". No such file or directory');
                }
            }

            /** reduce excluded and build files */
            $excludes = $this->getExclude();
            foreach ($this->_files as $i => $file) {
                foreach ($excludes as $exclude) {
                    if (strpos($file, realpath($exclude)) !== false) {
                        unset($this->_files[$i]);
                    }
                }
            }
        }

        return $this->_files;
    }

    /**
     * check build file
     *
     * @throws Core_Exception
     * @param string $file
     * @return void
     */
    protected function _checkBuildFile($file)
    {
        $dir = dirname($file);

        if (!is_dir($dir)) {
            throw new Core_Exception('"' . $dir . '" is not directory');
        }

        if (!is_writable($dir)) {
            throw new Core_Exception('"' . $dir . '" is not writable');
        }
    }

    /**
     * recursive scan directory
     *
     * @static
     * @param $path
     * @return array
     */
    static public function recursiveScanDir($path)
    {
        $files = array();
        $path = realpath($path);
        foreach (scandir($path) as $item) {
            if (in_array($item, array('.', '..'))) {
                continue;
            }

            $itemPath = $path . '/' . $item;
            if (is_file($itemPath)) {
                $files[] = $itemPath;
            } elseif (is_dir($itemPath)) {
                $files = array_merge($files, self::recursiveScanDir($itemPath));
            }
        }

        return $files;
    }
}

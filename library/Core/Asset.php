<?php

/**
 * Core_Asset
 *
 * asset manager
 */
class Core_Asset
{
    /**
     * @var string
     */
    protected $_dir = null;

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
     * @var null
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
     * set dir
     *
     * @param $dir
     * @return Core_Asset
     */
    public function setDir($dir)
    {
        $this->_dir = $dir;
        return $this;
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

    public function setAdapter($adapter)
    {

    }

    public function build()
    {

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
            if (null === $this->_buildDir) {
                throw new Core_Exception('Build dir is not set');
            }
            $this->_javascriptBuild = rtrim($this->_buildDir, '/') . '/'
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
            if (null === $this->_buildDir) {
                throw new Core_Exception('Build dir is not set');
            }
            $this->_stylesheetBuild = rtrim($this->_buildDir, '/') . '/'
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
            if (null === $this->_dir) {
                throw new Core_Exception('Asset Dir is not set');
            }

            if (!is_dir($this->_dir)) {
                throw new Core_Exception('"' . $this->_dir . '" is not directory');
            }

            $this->_files = array_reverse(self::recursiveScanDir($this->_dir));
        }

        return $this->_files;
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
        foreach (scandir($path) as $item) {
            if (in_array($item, array('.', '..'))) {
                continue;
            }

            $itemPath = rtrim($path, '/') . '/' . $item;
            if (is_file($itemPath)) {
                $files[] = $itemPath;
            } elseif (is_dir($itemPath)) {
                $files = array_merge($files, self::recursiveScanDir($itemPath));
            }
        }

        $files = array_reverse($files);
        return $files;
    }
}

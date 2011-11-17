<?php

/**
 * asset yui compressor adapter
 */
class Core_Asset_Adapter_YuiCompressor extends Core_Asset_Adapter_Abstract
{
    /**
     * @var string
     */
    protected $_jarPath = null;

    /**
     * build javascript files
     *
     * @param array $files
     * @param string $destination
     * @return void
     */
    public function buildJavascripts(array $files, $destination)
    {
        foreach ($files as $file) {
            $this->_compress($file, $destination);
        }
    }

    /**
     * build stylesheet files
     *
     * @param array $files
     * @param string $destination
     * @return void
     */
    public function buildStylesheets(array $files, $destination)
    {
        foreach ($files as $file) {
            $this->_compress($file, $destination);
        }
    }

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
     * set path to jar file
     *
     * @param $path
     * @return void
     */
    public function setJarPath($path)
    {
        $this->_jarPath = realpath($path);
    }

    /**
     * compress
     *
     * @throws Core_Exception
     * @param string $input
     * @param string $output
     * @return void
     */
    protected function _compress($input, $output)
    {
        if (null === $this->_jarPath) {
            throw new Core_Exception('Path tp jar file is not set');
        }

        if (!is_file($this->_jarPath)) {
            throw new Core_Exception('"' . $this->_jarPath . '" is not file');
        }

        if (!is_file($input)) {
            throw new Core_Exception('Input file "' . $input . '" does not exist');
        }

        $cmd = sprintf('java -jar %s %s >> %s', $this->_jarPath, $input, $output);
        exec($cmd);

        if (!is_file($output)) {
            throw new Core_Exception('Output file "' . $output . '" does not exist');
        }
    }
}

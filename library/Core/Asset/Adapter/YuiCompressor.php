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
        /**
         * yui can't compress source file
         * also it uses extension to detect type of compressing
         */
        $tempFile = str_replace('js', 'tmp.js', $destination);
        $this->_combine($files, $tempFile);
        $this->_compress($tempFile, $destination);
        unlink($tempFile);
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
        /**
         * yui can't compress source file
         * also it uses extension to detect type of compressing
         */
        $tempFile = str_replace('css', 'tmp.css', $destination);
        $this->_combine($files, $tempFile);
        $this->_compress($tempFile, $destination);
        unlink($tempFile);
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

        $cmd = sprintf('java -jar %s %s -o %s', $this->_jarPath, $input, $output);
        exec($cmd);

        if (!is_file($output)) {
            throw new Core_Exception('Output file "' . $output . '" does not exist');
        }
    }
}

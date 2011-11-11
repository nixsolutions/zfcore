<?php

/**
 * asset abstract adapter
 */
abstract class Core_Asset_Adapter_Abstract
{
    /**
     * build javascript files
     *
     * @abstract
     * @param array $files
     * @param string $destination
     * @return void
     */
    abstract public function buildJavascripts(array $files, $destination);

    /**
     * build stylesheet files
     *
     * @abstract
     * @param array $files
     * @param string $destination
     * @return void
     */
    abstract public function buildStylesheets(array $files, $destination);

    /**
     * combine files
     *
     * @param array $files
     * @param string $destination
     * @return void
     */
    protected function _combine(array $files, $destination)
    {
        $content = '';
        foreach ($files as $file) {
            if (is_file($file)) {
                $content .= file_get_contents($file) . "\n";
            }
        }
        file_put_contents($destination, $content);
    }
}

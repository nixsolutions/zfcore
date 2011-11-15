<?php

/**
 * asset simple adapter
 */
class Core_Asset_Adapter_Simple extends Core_Asset_Adapter_Abstract
{
    /**
     * build javascript files
     *
     * @param array $files
     * @param string $destination
     * @return void
     */
    public function buildJavascripts(array $files, $destination)
    {
        $this->_combine($files, $destination);
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
        $this->_combine($files, $destination);
    }
}

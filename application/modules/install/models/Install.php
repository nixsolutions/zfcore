<?php
/**
 * Install_Model_Install
 *
 * @author sm
 */
class Install_Model_Install
{
    /**
     * Genarate code
     *
     * @return string
     */
    public function generateCode()
    {
        return md5(uniqid(rand(), true));
    }

    /**
     * Save to filename
     *
     * @param string $code
     * @return string filename
     */
    public function saveCode($code)
    {
        $config = APPLICATION_PATH . '/modules/install/configs/checks.yaml';
        require_once 'Zend/Config/Yaml.php';
        require_once 'Core/Config/Yaml.php';
        $result = new Core_Config_Yaml($config);
        $requirements = $result->toArray();
        do {
            $filename = APPLICATION_PATH . '/../' . $requirements['directories']['cache_dir'] . '/' . uniqid() . '.php';
        } while (is_file($filename));

        file_put_contents(
            $filename,
            '<?php /*' . PHP_EOL . PHP_EOL . $code . PHP_EOL
        );
        return $filename;
    }
}
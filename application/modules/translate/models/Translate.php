<?php
/**
 * Model Translate
 *
 * @category Application
 * @package Model
 *
 * @version  $Id: Post.php 146 2010-07-05 14:22:20Z AntonShevchuk $
 */
class Translate_Model_Translate extends Core_Db_Table_Row_Abstract
{
    const ADAPTER = 'Zend_Translate_Adapter_Array';


    public static function getTranslation($locale)
    {
        return include self::getTranslationPath() . '/' . $locale . '.php';
    }

    public static function setTranslation($data, $locale)
    {
        $data = '<?php return ' . var_export($data, true) . ';';

        $path = self::getTranslationPath() . '/' . $locale . '.php';
        file_put_contents($path, $data);
        chmod($path, 0777);
    }

    public static function getTranslationPath()
    {
        return APPLICATION_PATH . '/../data/languages';
    }
}
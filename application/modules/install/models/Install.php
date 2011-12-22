<?php
/**
 * Install_Model_Install
 *
 * @author sm
 */
class Install_Model_Install
{
    const SESSION_DIR = '/../data/session';

    const CACHE_DIR = '/../data/cache';

    const LANGUAGES_DIR = '/../data/languages';

    const LOGS_DIR = '/../data/logs';

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
        do {
            $filename = APPLICATION_PATH . self::CACHE_DIR . '/' . uniqid() . '.php';
        } while (is_file($filename));

        file_put_contents(
            $filename,
            '<?php /*' . PHP_EOL . PHP_EOL . $code . PHP_EOL
        );
        return $filename;
    }
}
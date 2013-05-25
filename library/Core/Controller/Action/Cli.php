<?php
/**
 * User: naxel
 * Date: 22.05.13 16:12
 */

class Core_Controller_Action_Cli extends Zend_Controller_Action
{

    const INFO_MESSAGE = 'info';
    const ERROR_MESSAGE = 'error';
    const WARNING_MESSAGE = 'warning';
    const SUCCESS_MESSAGE = 'success';
    const DEFAULT_MESSAGE = 'default';


    private $_stdin;

    private $_enabledColorize = false;


    function preDispatch()
    {
        $this->flush();
    }


    function init()
    {
        if (PHP_SAPI !== 'cli') {
            throw new Core_Exception("Can't detect console");
        }

        if (Zend_Registry::isRegistered('console')) {
            $config = Zend_Registry::get('console');
            if ($config["colorize"] === true) {
                $this->_enabledColorize = true;
            }
        }

        $this->_helper->ViewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $this->adjustErrorHandler();
    }

    /**
     *
     */
    protected function adjustErrorHandler()
    {
        $errorHandler = $this->getFrontController()
            ->getPlugin('Zend_Controller_Plugin_ErrorHandler');

        if ($errorHandler) {
            $errorHandler->setErrorHandlerController('error');
        }
    }


    /**
     * @param $message
     * @return bool
     */
    public function confirmYes($message)
    {
        echo $message, ' [y/N] ';
        $answer = $this->readLine('N');

        return $answer == 'y';
    }


    /**
     * Read input string
     *
     * @param string $default
     * @return string
     */
    public function readLine($default = '')
    {
        $this->flush();

        if (empty ($this->_stdin)) {
            $this->_stdin = fopen('php://stdin', 'r');
        }

        $line = fgets($this->_stdin);
        $line = trim($line);

        if ('' == $line) {
            $line = $default;
        }

        return $line;
    }



    /**
     * @param $string
     * @param bool $newLine
     */
    public function writeLine($string, $newLine = true)
    {
        echo $string;
        echo $newLine ? PHP_EOL : '';
    }


    /**
     * Print message
     *
     * @param string $text
     * @param string $type
     * @param bool $date
     */
    public function printMessage($text, $type = self::DEFAULT_MESSAGE, $date = false)
    {
        if (!$this->_enabledColorize) {
            $type = false;
        }

        if ($date) {
            echo date("Y-m-d H:i:s") . ' ' . $this->_colorize($text, $type) . "\n";
        } else {
            echo $this->_colorize($text, $type) . "\n";
        }
    }


    /**
     * Add color to message
     *
     * @param string $text
     * @param string $color
     * @return string
     */
    private function _colorize($text, $color = self::DEFAULT_MESSAGE)
    {
        if (!$color) {
            return $text;
        }

        switch ($color) {
            case self::ERROR_MESSAGE:
                $color = "1;31m";
                break;
            case self::SUCCESS_MESSAGE:
                $color = "1;32m";
                break;
            case self::INFO_MESSAGE:
                $color = "1;36m";
                break;
            case self::WARNING_MESSAGE:
                $color = "1;33m";
                break;
            default:
                $color = "1;20m";
                break;
        }
        return "\033[" . $color . $text . " \033[m";
    }


    function flush()
    {
        while (ob_get_level()) {
            ob_end_flush();
        }
    }
}

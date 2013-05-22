<?php
/**
 * User: naxel
 * Date: 22.05.13 16:56
 */

class Tests_CliController extends Core_Controller_Action_Cli
{

    public function indexAction()
    {
        $this->printMessage('ZFCore CLI welcomes you!', Core_Controller_Action_Cli::SUCCESS_MESSAGE);
        $this->writeLine('Please enter your name:');
        $name = $this->readLine();
        $this->printMessage('Hi, ' . $name . '!', Core_Controller_Action_Cli::INFO_MESSAGE);
    }

}

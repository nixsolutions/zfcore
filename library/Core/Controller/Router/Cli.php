<?php
/**
 * User: naxel
 * Date: 22.05.13 16:48
 */

class Core_Controller_Router_Cli extends Zend_Controller_Router_Rewrite
{

    /** @var Zend_Controller_Request_Abstract */
    protected $currentRequest;


    /**
     * @param Zend_Controller_Request_Abstract $request
     * @return Zend_Controller_Request_Abstract
     */
    public function route(Zend_Controller_Request_Abstract $request)
    {
        $this->currentRequest = $request;

        $getOpt = new Zend_Console_Getopt(array());
        $arguments = $getOpt->getRemainingArgs();

        $module = 'index';
        $controller = 'index';
        $action = 'index';

        if ($arguments) {
            $module = array_shift($arguments);

            if ($arguments) {
                $controller = array_shift($arguments);

                if ($arguments) {
                    $action = array_shift($arguments);
                    $patternValidAction = '~^\w+[\-\w\d]+$~';
                    if (false == preg_match($patternValidAction, $action)) {
                        echo "Invalid action $action.\n", exit();
                    }

                    if ($arguments) {
                        foreach($arguments as $arg) {
                            $parameter = explode('=', $arg, 2);
                            if (false == isset($parameter[1])) {
                                $parameter[1] = true;
                            }

                            $request->setParam($parameter[0], $parameter[1]);
                            unset($parameter);
                        }
                    }
                }
            }
        }

        $request
            ->setModuleName($module)
            ->setControllerName($controller)
            ->setActionName($action);

        return $request;
    }

}

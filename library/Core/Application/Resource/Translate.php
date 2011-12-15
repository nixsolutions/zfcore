<?php

class Core_Application_Resource_Translate
    extends Zend_Application_Resource_Translate
{
    /**
     * Init Resource
     */
    public function init()
    {
        $translate = $this->getTranslate();
        $bootstrap = $this->getBootstrap();

        if (!empty($this->_options['locale'])) {
            $locale = $this->_options['locale'];
        }

        if (!empty($_COOKIE['locale'])) {
            $locale = $_COOKIE['locale'];
        } elseif ($bootstrap->hasResource('Locale')) {
            $locale = $bootstrap->bootstrap("Locale")
                                ->getResource('Locale')->getLanguage();
        }
        if (empty($_COOKIE['locale'])) {
            setcookie('locale', $locale, time()+60*60*24*30);
        }

        $router = $bootstrap->bootstrap('Router')->getResource('Router');
        $langRoute = new Zend_Controller_Router_Route(
            ':locale',
            array(
                'locale' => $locale
            ),
            array('locale' => '^[a-z]{2}$')
        );

        $router->addDefaultRoutes();

        foreach ($router->getRoutes() as $name => $route) {
            $router->removeRoute($name);

            if ('default' == $name) {
                $router->addRoute($name . 'Default', $route);
            }

            $router->addRoute($name, $langRoute->chain($route));

            if ($route instanceof Zend_Controller_Router_Route_Regex
                || $route instanceof Zend_Controller_Router_Route_Static) {
                $router->addRoute($name . 'Default', $route);
            }

        }

        return $translate;
    }
}
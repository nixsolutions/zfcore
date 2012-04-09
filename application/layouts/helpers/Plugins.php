<?php

/**
 * add some plugins
 *
 * @category Application
 * @package  Core_View
 * @subpackage Helper
 */
class Application_View_Helper_Plugins extends Zend_View_Helper_Abstract
{
    /**
     * @var boolean
     */
    protected static $_wysiwyg;

    /**
     * @var boolean
     */
    protected static $_redactor;

    /**
     * @var boolean
     */
    protected static $_elfinder;

    /**
     * @var boolean
     */
    protected static $_fancybox;

    /**
     * add some plugins
     *
     * @return Application_View_Helper_Plugins
     */
    public function plugins()
    {
        return $this;
    }

    /**
     * add redactor
     *
     * @return Application_View_Helper_Plugins
     */
    public function redactor()
    {
        if (!self::$_redactor) {
            $view = $this->view;
            $view->headScript()->appendFile(
                $view->baseUrl('js/redactor/redactor.js')
            );
            $view->headLink()->appendStylesheet(
                $view->baseUrl('js/redactor/css/redactor.css')
            );

            self::$_redactor = true;
        }

        return $this;
    }

    /**
     * add wysiwyg
     *
     * @return Application_View_Helper_Plugins
     */
    public function wysiwyg()
    {
        if (!self::$_wysiwyg) {
            $view = $this->view;
            $view->headScript()->appendFile(
                $view->baseUrl('js/wysiwyg/wysiwyg.js')
            );

            $view->headLink()->appendStylesheet(
                $view->baseUrl('js/wysiwyg/buttons.css')
            )->appendStylesheet(
                $view->baseUrl('js/wysiwyg/style.css')
            );

            self::$_wysiwyg = true;
        }

        return $this;
    }

    /**
     * add elfinder
     *
     * @return Application_View_Helper_Plugins
     */
    public function elfinder()
    {
        if (!self::$_elfinder) {
            $view = $this->view;
            $view->headLink()->appendStylesheet(
                $view->baseUrl('js/elfinder/css/elfinder.full.css')
            )
            ->appendStylesheet(
                $view->baseUrl('js/elfinder/css/theme.css')
            );

            $view->headScript()->appendFile(
                $view->baseUrl('js/elfinder/js/elfinder.full.js')
            );
            self::$_elfinder = true;
        }

        return $this;
    }

    /**
     * Add fancybox plugin
     *
     * @return Application_View_Helper_Plugins
     */
    public function fancybox()
    {
        if (!self::$_fancybox) {
            $view = $this->view;
            $view->headLink()->appendStylesheet(
                $view->baseUrl('js/fancybox/css/fancybox.css')
            );

            $view->headScript()->appendFile(
                $view->baseUrl('js/fancybox/js/fancybox.pack.js')
            );

            self::$_fancybox = true;
        }

        return $this;
    }
}

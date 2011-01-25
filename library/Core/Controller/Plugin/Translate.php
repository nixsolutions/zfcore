<?php
/**
 * Front Controller Plugin.
 * Hooks routeStartup, dispatchLoopStartup.
 * Multilanguage support, which adds language detection by URL prefix.
 *
 * @category   Core
 * @package    Core_Controller
 * @subpackage Plugins
 *
 * @version    $Id: Translate.php 168 2010-07-19 16:15:50Z dmitriy.britan $
 */

class Core_Controller_Plugin_Translate extends Zend_Controller_Plugin_Abstract
{
    /**
     * Options
     * 
     * @var array
     */
    protected $_options = array();
    
    /**
     * Default language
     *
     * @var string
     */
    protected $_default = 'en';
    
    /**
     * Current language
     *
     * @var string
     */
    protected $_language = 'en';

    /**
     * Map of supported locales.
     *
     * @var array
     */
    protected $_locales = array('en' => 'en_GB');

    /**
     * URL delimetr symbol.
     * @var string
     */
    protected $_urlDelimiter = '/';

    /**
     * HTTP status code for redirects
     * @var int
     */
    protected $_redirectCode = 302;

    /**
     * Contructor
     * Verify options
     *
     * @param array $options
     */
    public function __construct(Array $options = array())
    {
        if (isset($options['locales'])) {
            $this->_locales = array_merge($this->_locales, $options['locales']);
            unset($options['locales']);
        }
        
        if (isset($options['default'])
            && array_key_exists($options['default'], $this->_locales)
            ) {
            $this->_default = $options['default'];
            unset($options['default']);
        }
        
        $this->_options = array_merge($this->_options, $options);
    }

    /**
     * routeStartup() plugin hook
     * Parse URL and extract language if present in URL. Prepare base url for routing.
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        // Work only with http request
//        if (!($request instanceof Zend_Controller_Request_Http)) {
//            return false;
//        }
            
        // switch statement for 
        switch (true) {
            case $this->_checkUrl($request):
                // check URL
                break;
            case $this->_checkSession($request):
                // check Session
                break;
            case $this->_checkBrowser($request):
                // check Browser
                break;
            case $this->_checkDefault($request):
            default:
                // it's default
                break;
        }
    }
    
    /**
     * check language from URL
     *
     * language present in URL after baseUrl. (http://host/base_url/en/..., /ru, /rus...)
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @return  bool
     */
    private function _checkUrl(Zend_Controller_Request_Abstract $request) 
    {
        // save to session
        $session = new Zend_Session_Namespace();
        
        if (preg_match("#^/([a-zA-Z]{2})($|/)#", $request->getPathInfo(), $matches)) {
            $lang = $matches[1];
            if (($this->_default == $lang && $request->isGet())
                or !array_key_exists($lang, $this->_locales)) {
                
                // save to session
                $session->language = $this->_default;
                // redirect to page without language prefix
                $request->setPathInfo(substr($request->getPathInfo(), strlen($lang)+1));
                $this->_doRedirectAndExit($request);
                return true;
            } else {
                // save to session
                $session->language = $lang;
                $this->_language = $lang;
                $this->_rewriteBaseUrl($request);
                return true;
            }
        } else {
            return false;
        }
    }
    
    /**
     * check language from browser
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @return  bool
     */
    private function _checkSession(Zend_Controller_Request_Abstract $request) 
    {
        $session = new Zend_Session_Namespace();
        if (isset($session->language)) {
            $this->_language = $session->language;
            if ($this->_language != $this->_default && $request->isGet()) {
                $this->_doRedirectAndExit($request, $this->_language);
            }
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * check language from browser
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return  bool
     */
    private function _checkBrowser(Zend_Controller_Request_Abstract $request) 
    {
        try {
            if ($locale = new Zend_Locale(Zend_Locale::BROWSER)) {
                $this->_language = $locale->getLanguage();
                if ($this->_language != $this->_default && $request->isGet()) {
                    $this->_doRedirectAndExit($request, $this->_language);
                }
                return true;
            }
        } catch (Exception $e) { 
        }

        return false;
    }

    /**
     * check default language
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return  bool
     */
    private function _checkDefault(Zend_Controller_Request_Abstract $request) 
    {
        $this->_language = $this->_default;
        return true;
    }
    
    /**
     * rewrite BaseUrl
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return  void
     */
    private function _rewriteBaseUrl(Zend_Controller_Request_Abstract $request) 
    {
        // Front Controller
        $front = Zend_Controller_Front::getInstance();
        // save original base URL
        $baseUrl = $front->getBaseUrl();
        Zend_Registry::set('baseUrl', $baseUrl);
        // change base URL
        $front->setBaseUrl($baseUrl . $this->_urlDelimiter . $this->_language);
        // init path info with new baseUrl.
        $request->setPathInfo(substr($request->getPathInfo(), strlen($this->_language)+1));
    }
    
    /**
     * Redirect
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param string $lang
     */
    protected function _doRedirectAndExit(Zend_Controller_Request_Abstract $request, $lang = null)
    {
        // set evaluating language in URL, and redirect request
        $uri = Zend_Uri::factory($request->getScheme());
        $uri->setHost($request->getHttpHost());
        if ($lang) {
            $uri->setPath(
                $request->getBaseUrl()
                . $this->_urlDelimiter
                . $lang . $request->getPathInfo()
            );
        } else {
            $uri->setPath($request->getBaseUrl() . $request->getPathInfo());
        }
        
        $query = '';
        $requestUri = $request->getRequestUri();
        if (false !== ($pos = strpos($requestUri, '?'))) {
            $query = substr($requestUri, $pos + 1);
            $uri->setQuery($query);
        }
        $response = Zend_Controller_Front::getInstance()->getResponse();
        $response->setRedirect($uri, $this->_redirectCode);
        $response->sendHeaders();
        exit();
    }
    
    
    
    /**
     * routeShutdown
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        // Work only with http request
//        if (!($request instanceof Zend_Controller_Request_Http)) {
//            return false;
//        }
        
        // location detector
        if (Zend_Registry::isRegistered('locale')) {
            // from user options
            $localeString = Zend_Registry::get('locale');
        } else {
            $localeString = $this->_locales[$this->_language];
        }
        
        $locale    = new Zend_Locale($localeString);
        $this->_options['locale'] = $locale;
        
        if ($this->_options['logUntranslated']) {
            // Create a log instance
            $writer = new Zend_Log_Writer_Stream($this->_options['logPath']);
            $this->_options['log'] = new Zend_Log($writer);
        }
        
        $translate = new Zend_Translate($this->_options);

        Zend_Registry::set('Zend_Locale', $locale);
        Zend_Registry::set('Zend_Translate', $translate);
        Zend_Validate_Abstract::setDefaultTranslator($translate);
        Zend_Form::setDefaultTranslator($translate);
    }  
   
    /**
     * preDispatch
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // TODO: add translation for current module
//        $translate = Zend_Registry::get('Zend_Translate');
//        $translate->addTranslation(APPLICATION_PATH . '/modules/'.$request->getModuleName().'/languages/',
//                                   Zend_Registry::get('Zend_Locale'));
    }

}
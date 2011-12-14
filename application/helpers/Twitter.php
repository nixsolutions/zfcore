<?php
/**
 * Helper_Twitter
 *
 * @version $Id$
 */
class Helper_Twitter extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Zend_Oauth_Consumer
     */
    protected $_client;

    /**
     * @var Zend_Oauth_Token
     */
    protected $_token;

    /**
     * @var Zend_Service_Twitter
     */
    protected $_service;

    /**
     * Init Zend_Oauth_Consumer
     */
    public function direct()
    {
        return $this->getClient();
    }

    /**
     * Get fb client
     *
     * @return Zend_Oauth_Consumer
     */
    public function getClient()
    {
        if (!$this->_client) {
            if (!Zend_Registry::isRegistered('twitterConfig')) {
                throw new Zend_Controller_Action_Exception(
                    'Twitter Config not found'
                );
            }
            $config = Zend_Registry::get('twitterConfig');

            if (strpos($config['callbackUrl'], 'http') !== 0) {
                $view = new Zend_View();
                $config['callbackUrl'] = $view->serverUrl(
                    $config['callbackUrl']
                );
            }
            $this->setClient(new Zend_Oauth_Consumer($config));
        }

        return $this->_client;
    }

    /**
     * Set client
     *
     * @param Zend_Oauth_Consumer $client
     * @return Helper_Twitter
     */
    public function setClient(Zend_Oauth_Consumer $client)
    {
        $this->_client = $client;

        return $this;
    }

    /**
     * Get Service
     *
     * @return Zend_Service_Twitter
     */
    public function getService()
    {
        if (!$this->_service) {
            $token = $this->getToken();

            $this->_service = new Zend_Service_Twitter(
                array('username' => $token->getParam('screen_name'),
                      'accessToken' => $token)
            );
        }
        return $this->_service;
    }

    /**
     * Get Access Token
     *
     * @return Zend_Oauth_Token_Access|null
     */
    public function getToken()
    {
        if (!$this->_token) {

            $query = $this->getRequest()->getQuery();

            if (!empty($query) && isset($_SESSION['TWITTER_REQUEST_TOKEN'])) {
                $token = $this->getClient()->getAccessToken(
                    $query,
                    unserialize($_SESSION['TWITTER_REQUEST_TOKEN'])
                );
                $_SESSION['TWITTER_ACCESS_TOKEN'] = serialize($token);

                // Now that we have an Access Token, we can discard the Request Token
                $_SESSION['TWITTER_REQUEST_TOKEN'] = null;

                $this->_token = $token;
            }
        }
        return $this->_token;
    }

    /**
     * Get info
     *
     * @return ArrayObject
     */
    public function getInfo()
    {
        if ($token = $this->getToken()) {

            $info = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
            $info->twId      = $token->getParam('user_id');
            $info->login     = $token->getParam('screen_name');
            $info->firstname = $info->login;

            return $info;
        } else {
            $consumer = $this->getClient();
            // fetch a request token
            $token = $consumer->getRequestToken();

            // persist the token to storage
            $_SESSION['TWITTER_REQUEST_TOKEN'] = serialize($token);

            // redirect the user
            $consumer->redirect();
        }
    }
}
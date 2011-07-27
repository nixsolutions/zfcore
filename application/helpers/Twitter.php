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
     * Login
     *
     */
    public function login()
    {
        if ($token = $this->getToken()) {
            $users = new Users_Model_Users_Table();
            if (!$row = $users->getByTwid($token->getParam('user_id'))) {
                $row = $users->createRow();
                $row->twId = $token->getParam('user_id');
                $row->login = $token->getParam('screen_name');
                $row->firstname = $row->login;
                $row->role = Users_Model_User::ROLE_USER;
                $row->status = Users_Model_User::STATUS_ACTIVE;
            }
            $row->logined = date('Y-m-d H:i:s');
            $row->ip = $this->getRequest()->getClientIp();
            $row->count++;
            $row->save();

            $row->login();
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
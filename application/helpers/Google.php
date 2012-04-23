<?php
/**
 * Helper_Google
 */
class Helper_Google extends Zend_Controller_Action_Helper_Abstract
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
     * Init Zend_Oauth_Consumer
     * @return \Zend_Oauth_Consumer
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
            $this->setClient(new Zend_Oauth_Consumer($this->getConfig()));
        }

        return $this->_client;
    }

    /**
     * Get config
     *
     * @throws Zend_Controller_Action_Exception
     * @return array
     */
    public function getConfig()
    {
        if (!Zend_Registry::isRegistered('googleConfig')) {
            throw new Zend_Controller_Action_Exception(
                'Google Config not found'
            );
        }
        $config = Zend_Registry::get('googleConfig');

        if (strpos($config['callbackUrl'], 'http') !== 0) {
            $view = new Zend_View();
            $config['callbackUrl'] = $view->serverUrl(
                $config['callbackUrl']
            );
        }
        return $config;
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
     * Get Access Token
     *
     * @return Zend_Oauth_Token_Access|null
     */
    public function getToken()
    {
        if (!$this->_token) {

            $query = $this->getRequest()->getQuery();

            if (!empty($query) && isset($_SESSION['GOOGLE_REQUEST_TOKEN'])) {
                $token = $this->getClient()->getAccessToken(
                    $query,
                    unserialize($_SESSION['GOOGLE_REQUEST_TOKEN'])
                );
                $_SESSION['GOOGLE_ACCESS_TOKEN'] = serialize($token);

                // Now that we have an Access Token, we can discard the Request Token
                $_SESSION['GOOGLE_REQUEST_TOKEN'] = null;

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
            $client = $token->getHttpClient($this->getConfig());
            $client->setUri('https://www-opensocial.googleusercontent.com/api/people/@me/@self');
            $client->setMethod(Zend_Http_Client::GET);

            $data = Zend_Json::decode($client->request()->getBody());

            $info = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
            $info->login     = $data['entry']['displayName'];
            $info->firstname = $data['entry']['name']['givenName'];
            $info->lastname  = $data['entry']['name']['familyName'];
            $info->gId       = $data['entry']['id'];

            $client->setUri('https://www.googleapis.com/userinfo/email');
            $emailData = explode('&', $client->request()->getBody());

            $info->email = substr($emailData['0'], 6);

            return $info;
        } else {
            $consumer = $this->getClient();
            // fetch a request token
            $token = $consumer->getRequestToken(
                array(
                     'scope' =>
                     'http://www-opensocial.googleusercontent.com/api/people/ '
                     . 'https://www.googleapis.com/auth/userinfo#email')
            );

            // persist the token to storage
            $_SESSION['GOOGLE_REQUEST_TOKEN'] = serialize($token);

            // redirect the user
            $consumer->redirect();
        }
    }
}
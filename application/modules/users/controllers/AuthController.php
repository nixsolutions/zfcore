<?php
/**
 * User: naxel
 * Date: 31.05.13 16:29
 */

class Users_AuthController extends Core_Controller_Action
{

    /**
     * Default Opauth config
     *
     * @var array
     */
    private $_opauthConfig = array(
        /**
         * Path where Opauth is accessed.
         *  - Begins and ends with /
         *  - eg. if Opauth is reached via http://example.org/auth/, path is '/auth/'
         *  - if Opauth is reached via http://auth.example.org/, path is '/'
         */
        'path' => '/users/auth/',
        //Callback URL: redirected to after authentication, successful or otherwise
        'callback_url' => '/users/auth/callback/',
        //A random string used for signing of $auth response.
        'security_salt' => null,
        // Define strategies and their respective configs here
        'Strategy' => array()
    );


    /**
     * Init controller plugins
     */
    public function init()
    {
        /* Initialize action controller here */
        parent::init();
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');

        //Set Main config
        if (Zend_Registry::isRegistered('opauthConfig')) {
            $opauthConfig = Zend_Registry::get('opauthConfig');
            if (
                isset($opauthConfig['path']) && $opauthConfig['path']
                && isset($opauthConfig['callback_url']) && $opauthConfig['callback_url']
                && isset($opauthConfig['security_salt']) && $opauthConfig['security_salt']
            ) {
                $this->_opauthConfig['path'] = $opauthConfig['path'];
                $this->_opauthConfig['callback_url'] = $opauthConfig['callback_url'];
                $this->_opauthConfig['security_salt'] = $opauthConfig['security_salt'];
            } else {
                throw new Zend_Controller_Action_Exception('Opauth is not configured.');
            }
        }

        //Set Facebook strategy
        if (Zend_Registry::isRegistered('fbConfig')) {
            $fbConfig = Zend_Registry::get('fbConfig');
            if ($fbConfig['appId']) {
                $this->_opauthConfig['Strategy']['Facebook'] = array(
                    'app_id' => $fbConfig['appId'],
                    'app_secret' => $fbConfig['secret'],
                    'scope' => $fbConfig['scope']
                );
            }
        }

        //Set Twitter strategy
        if (Zend_Registry::isRegistered('twitterConfig')) {
            $twitterConfig = Zend_Registry::get('twitterConfig');
            if ($twitterConfig['consumerKey']) {
                $this->_opauthConfig['Strategy']['Twitter'] = array(
                    'key' => $twitterConfig['consumerKey'],
                    'secret' => $twitterConfig['consumerSecret']
                );
            }
        }

        //Set Google strategy
        if (Zend_Registry::isRegistered('googleConfig')) {
            $googleConfig = Zend_Registry::get('googleConfig');
            if ($googleConfig['clientId']) {
                $this->_opauthConfig['Strategy']['Google'] = array(
                    'client_id' => $googleConfig['clientId'],
                    'client_secret' => $googleConfig['clientSecret']
                );
            }
        }

        require_once 'autoload.php';
    }


    /**
     * Call index action with strategy name
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function indexAction()
    {
        if (array_key_exists(ucfirst($this->_getParam('provider')), $this->_opauthConfig['Strategy'])
        ) {
            new Opauth($this->_opauthConfig);
        } else {
            throw new Zend_Controller_Action_Exception('Strategy not found');
        }
    }


    /**
     * Callback action. Redirected after auth
     */
    public function callbackAction()
    {
        $response = null;
        try {
            $opauth = new Opauth($this->_opauthConfig, false);
            if (isset($opauth->env['callback_transport'])
                && $opauth->env['callback_transport'] == 'session'
            ) {

                if (isset($_SESSION['opauth'])) {

                    $response = $_SESSION['opauth'];
                    unset($_SESSION['opauth']);

                    /**
                     * Check if it's an error callback
                     */
                    if (array_key_exists('error', $response)) {

                        throw new Zend_Controller_Action_Exception(
                            'Opauth returns error auth response'
                        );

                    } else {
                        if (empty($response['auth'])
                            || empty($response['timestamp'])
                            || empty($response['signature'])
                            || empty($response['auth']['provider'])
                            || empty($response['auth']['uid'])
                        ) {

                            throw new Zend_Controller_Action_Exception(
                                'Invalid auth response: Missing key auth response components.'
                            );

                        } elseif (!$opauth->validate(
                            sha1(print_r($response['auth'], true)),
                            $response['timestamp'],
                            $response['signature'], $reason)
                        ) {
                            //Auth response validation error
                            throw new Zend_Controller_Action_Exception('Invalid auth response: ' . $reason);

                        } else {
                            //Auth Ok!
                            $this->_oauthLogin($response);

                        }
                    }
                } else {
                    throw new Zend_Controller_Action_Exception('Auth session is empty.');
                }
            } else {
                throw new Zend_Controller_Action_Exception('Incorrect callback transport.');
            }
        } catch (Exception $ex) {
            //Display error and redirect to login page
            $this->_flashMessenger->addMessage($ex->getMessage());
            $this->_helper->redirector->gotoRoute(array(), 'login');
        }
    }


    /**
     * @param array $authData
     * @throws Zend_Controller_Action_Exception
     */
    private function _oauthLogin($authData)
    {
        if (isset($authData['auth']['uid'])) {

            $users = new Users_Model_User_Table();

            switch ($authData['auth']['provider']) {
                case 'Facebook':
                    $serviceFieldName = 'facebookId';
                    $row = $users->getByFacebookid($authData['auth']['uid']);
                    if (!$row) {
                        if (isset($authData['auth']['info']['email'])) {
                            //If exist user's email
                            $row = $users->getByEmail($authData['auth']['info']['email']);
                            if ($row) {
                                $row->facebookId = $authData['auth']['uid'];
                                $row->save();
                            }
                        }
                    }
                    break;
                case 'Twitter':
                    $serviceFieldName = 'twitterId';
                    $row = $users->getByTwitterid($authData['auth']['uid']);
                    break;
                case 'Google':
                    $serviceFieldName = 'googleId';
                    $row = $users->getByGoogleid($authData['auth']['uid']);
                    if (!$row) {
                        if (isset($authData['auth']['info']['email'])) {
                            $authData['auth']['info']['nickname'] = $authData['auth']['info']['email'];
                            //If exist user's email
                            $row = $users->getByEmail($authData['auth']['info']['email']);
                            if ($row) {
                                $row->googleId = $authData['auth']['uid'];
                                $row->save();
                            }
                        }
                    }
                    break;
                default:
                    throw new Zend_Controller_Action_Exception('Incorrect provider.');
                    break;
            }

            //Create user
            if (!$row) {

                if ($users->getByLogin($authData['auth']['info']['nickname'])) {
                    //Is not allow nickname
                    throw new Zend_Controller_Action_Exception('Login is occupied.');
                } else {
                    //Is allow nickname
                    $row = $users->createRow();

                    //Insert user data if exist
                    if (isset($authData['auth']['info']['nickname'])) {
                        $row->login = $authData['auth']['info']['nickname'];
                    }
                    if (isset($authData['auth']['info']['email'])) {
                        $row->email = $authData['auth']['info']['email'];
                    }
                    if (isset($authData['auth']['info']['first_name'])) {
                        $row->firstname = $authData['auth']['info']['first_name'];
                    }
                    if (isset($authData['auth']['info']['last_name'])) {
                        $row->lastname = $authData['auth']['info']['last_name'];
                    }

                    //service userId
                    $row->$serviceFieldName = $authData['auth']['uid'];

                    $row->role = Users_Model_User::ROLE_USER;
                    $row->status = Users_Model_User::STATUS_ACTIVE;
                    $row->save();
                }
            }

            $row->login();

            $this->_helper->flashMessenger->addMessage('Now You\'re Logging!');
            $this->_helper->redirector(false, false, false);

        } else {
            throw new Zend_Controller_Action_Exception('Invalid auth response.');
        }

    }


}

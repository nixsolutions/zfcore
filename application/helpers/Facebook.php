<?php
/**
 * Helper_Facebook
 */
class Helper_Facebook extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Facebook_Facebook
     */
    protected $_client;

    const FB_APP_URL = "http://apps.facebook.com/";

    /**
     * Init fb client
     * @return \Facebook_Facebook
     */
    public function direct()
    {
        return $this->getClient();
    }

    /**
     * Get fb client
     *
     * @throws Zend_Controller_Action_Exception
     * @return Facebook_Facebook
     */
    public function getClient()
    {
        if (!$this->_client) {
            if (!Zend_Registry::isRegistered('fbConfig')) {
                throw new Zend_Controller_Action_Exception(
                    'Facebook Connect: config not found'
                );
            }
            $config = Zend_Registry::get('fbConfig');

            if (empty($config['appId'])) {
                throw new Zend_Controller_Action_Exception(
                    'Facebook Connect: application Id is missed'
                );
            }

            $this->setClient($client = new Facebook_Facebook($config));
        }

        return $this->_client;
    }

    /**
     * Set client
     *
     * @param Facebook_Facebook $client
     * @return Helper_Facebook
     */
    public function setClient($client)
    {
        $this->_client = $client;

        return $this;
    }

    /**
     * Get info
     *
     * @return ArrayObject
     */
    public function getInfo()
    {
        $info = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);

        $user = $this->getClient()->api("/me");

        $info->login     = $user['username'];
        $info->email     = $user['email'];
        $info->firstname = $user['first_name'];
        $info->lastname  = $user['last_name'];
        $info->facebookId = $user['id'];

        return $info;
    }
}
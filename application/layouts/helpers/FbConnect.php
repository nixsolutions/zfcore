<?php
/**
 * Application_View_Helper_FbConnect
 *
 * @version $id$
 */
class Application_View_Helper_FbConnect extends Zend_View_Helper_Abstract
{
    /**
     * @var boolen
     */
    protected $_xfbml = true;

    /**
     * @var string
     */
    protected $_appId;

    /**
     * @var boolen
     */
    protected $_cookie;

    /**
     * @var boolen
     */
    protected $_status = true;

    /**
     * @var boolen
     */
    protected $_loaded = false;

    /**
     * Set defaults
     *
     * @param boolen  $status
     * @param boolen  $xfbml
     * @return Application_View_Helper_FbConnect
     */
    public function fbConnect($status = null, $xfbml = null)
    {
        if (!$this->_appId) {
            $config = Zend_Registry::get('fbConfig');
            $this->_appId = $config['appId'];
            $this->_cookie = empty($config['cookie']) ? false : true;
        }
        if (null !== $status) {
            $this->_status = $status;
        }
        if (null !== $xfbml) {
            $this->_xfbml = $xfbml;
        }

        return $this;
    }

    /**
     * Draw login button
     *
     * @param string $label
     * @param array  $perms
     * @param string $onLogin
     * @return Application_View_Helper_FbConnect
     */
    public function login($label = 'Login with Facebook', array $perms = array('email', 'user_checkins'), $onLogin = '')
    {
        ?>

        <fb:login-button onlogin="<?php echo $onLogin?>" perms="<?php echo join(',', $perms)?>"><?php echo $label?></fb:login-button>

        <?php

        return $this;
    }

    /**
     * Draw registration form
     *
     * @param array  $fields
     * @param string $callback
     * @return Application_View_Helper_FbConnect
     */
    public function register(array $fields, $callback = '')
    {
        if ($callback) {
            $callback = urlencode($callback);
        }
        ?>

        <fb:registration fields="<?php echo Zend_Json::encode($fields)?>"
                         redirect-uri="<?php echo $callback?>"></fb:registration>

        <?php

        return $this;
    }

    /**
     * Load the JavaScript SDK into your page and initialize it with your appId
     *
     * @return string
     */
    public function __toString()
    {
        if (!$this->_loaded) {
            ?>

            <div id="fb-root"></div>
            <script type="text/javascript" src="http://connect.facebook.net/en_US/all.js"></script>
            <script type="text/javascript">
                FB.init({
                    appId: '<?php echo $this->_appId?>',
                    cookie: <?php echo ($this->_cookie) ? 'true' : 'false'?>,
                    status: <?php echo ($this->_status) ? 'true' : 'false'?>,
                    xfbml: <?php echo ($this->_xfbml) ? 'true' : 'false'?>
                });
            </script>

            <?php
            $this->_loaded = true;
        }
        return '';
    }
}
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
    protected $_oauth = true;

    /**
     * @var boolen
     */
    protected $_loaded = false;

    /**
     * Set defaults
     *
     * @param boolen $status
     * @param boolen $xfbml
     * @param boolen $oauth
     * @return Application_View_Helper_FbConnect
     */
    public function fbConnect($status = null, $xfbml = null, $oauth = null)
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
        if (null !== $oauth) {
            $this->_oauth = $oauth;
        }

        return $this;
    }

    /**
     * Draw login button
     *
     * @param string $label
     * @param array  $perms
     * @param string $uri
     * @param array  $params
     * @return Application_View_Helper_FbConnect
     */
    public function login(
        $label        = 'Login with Facebook',
        array $perms  = array('email', 'user_checkins'),
        $uri          = '',
        array $params = array())
    {
        $params['next'] = $uri;
        $params['req_perms'] = join(',', $perms);

        $facebook = new Facebook_Facebook(Zend_Registry::get('fbConfig'));
        ?>

		<a href="<?php echo $facebook->getLoginUrl($params)?>"><?php echo $label?></a>

        <?php
        return $this;
    }

    /**
     * Draw registration form
     *
     * @param array  $fields
     * @param string $uri
     * @return Application_View_Helper_FbConnect
     */
    public function register(array $fields, $uri = '')
    {
        ?>

        <div class="fb-registration"
             data-fields="<?php echo Zend_Json::encode($fields)?>"
             data-redirect-uri="<?php echo $uri?>"></div>

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
             <script type="text/javascript">
                window.fbAsyncInit = function() {
                    FB.init({
                        appId: '<?php echo $this->_appId?>',
                        cookie: <?php echo ($this->_cookie) ? 'true' : 'false'?>,
                        status: <?php echo ($this->_status) ? 'true' : 'false'?>,
                        xfbml: <?php echo ($this->_xfbml) ? 'true' : 'false'?>,
                        oauth: <?php echo ($this->_oauth) ? 'true' : 'false'?>
                    });
                };
                (function(d){
                    var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
                    js = d.createElement('script'); js.id = id; js.async = true;
                    js.src = "//connect.facebook.net/en_US/all.js";
                    d.getElementsByTagName('head')[0].appendChild(js);
                  }(document));
            </script>

            <?php
            $this->_loaded = true;
        }
        return '';
    }
}
<?php
/**
 * This is the DbTable class for the users table.
 *
 * @category Application
 * @package Model
 * @subpackage DbTable
 *
 * @version  $Id: Manager.php 163 2010-07-12 16:30:02Z AntonShevchuk $
 */
class Users_Model_Users_Manager extends Core_Model_Manager
{
    /**
     * Zend_Auth_Result
     *
     * @param string $login
     * @param string $password
     *
     * @return  bool
     */
    public static function authenticate($login, $password)
    {
        $authAdapter = new Zend_Auth_Adapter_DbTable(
            Zend_Db_Table::getDefaultAdapter(),
            'users',
            'login',
            'password',
            'MD5(CONCAT(salt, ?)) AND ' .
            'status = "'.Users_Model_User::STATUS_ACTIVE.'"'
        );

        $auth = Zend_Auth::getInstance();

        // set the input credential values to authenticate against
        $authAdapter->setIdentity($login);
        $authAdapter->setCredential($password);

        // do the authentication
        $result =  $auth->authenticate($authAdapter);

        if ($result->isValid()) {
            // success: store database row to auth's storage system
            $users = new Users_Model_Users_Table();
            $auth->getStorage()->write($users->getByLogin($login));
            return true;
        }
        return false;
    }

    /**
     * Login user
     *
     * @param array $data
     * @return bool
     */
    public function login($data)
    {
        if ($this->authenticate($data['login'], $data['password'])) {
            $user = $this->getDbTable()->getByLogin($data['login']);

            $user->logined = date('Y-m-d H:i:s');
            $user->ip      = $this->_getIpFromRequest();
            $user->count++;
            $user->save();

            if (!empty($data['remember'])) {
                Zend_Session::rememberMe(60*60*24*14);
            }
            return true;
        }
        return false;
    }

    /**
     * Logout user
     */
    public static function logout()
    {
        Zend_Auth::getInstance()->clearIdentity();
    }

    /**
     * Register new user
     *
     * @param array $data
     * @return bool|Users_Model_User
     */
    public function register($data)
    {
        $data = array_merge(
            $data,
            array(
                'role'     => Users_Model_User::ROLE_USER,
                'status'   => Users_Model_User::STATUS_REGISTER,
                'hashCode' => md5($data['login'] . uniqid()),
                'ip'       => $this->_getIpFromRequest()
            )
        );
        $user = $this->getDbTable()->createRow($data);
        if ($user->save()) {
            return $user;
        }
        return false;
    }

    /**
     * Confirm registration
     *
     * @param string $aHash
     * @return bool
     */
    public function confirmRegistration($aHash)
    {
        $user = $this->getDbTable()->getByHashcodeAndStatus(
            $aHash,
            Users_Model_User::STATUS_REGISTER
        );
        if ($user) {
            if ($user->id) {
                $user->hashCode = null;
                $user->status   = Users_Model_User::STATUS_ACTIVE;
                $user->save();

                return true;
            }
        }
        return false;
    }

    /**
     * Forget password
     *
     * @param string $aEmail
     * @return bool
     */
    public function forgetPassword($aEmail)
    {
        $user = $this->getDbTable()->getByEmail($aEmail);
        if ($user) {
            if ($user->id) {
                $user->hashCode = md5($user->login . uniqid());
                $user->save();
                return $user;
            }
        }
        return false;
    }


    /**
     * Set user password
     * @param string $userHash
     * @param string $userPassword
     * @return bool
     */
    public function setPassword($userHash, $userPassword)
    {
        $user = $this->getDbTable()->getByHashcode($userHash);
        if ($user) {
            if ($user->id) {
                if ($userPassword) { //confirm to change password
                    $user->password = $userPassword;
                    $user->hashCode = null;
                    $user->save();
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Clear user hash
     * @param string $userHash
     * @return bool
     */
    public function clearHash($userHash)
    {
        $user = $this->getDbTable()->getByHashcode($userHash);
        if ($user) {
            if ($user->id) {
                $user->hashCode = null;
                $user->save();
                return true;
            }
        }
        return false;
    }

    /**
     * Generate random password
     *
     * @return string
     */
    public function generatePassword()
    {
        $randStr = '';
        $feed = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        for ($i = 0; $i < Users_Model_User::MIN_PASSWORD_LENGTH; $i++) {
            $randStr .= substr($feed, rand(0, strlen($feed) - 1), 1);
        }
        return $randStr;
    }

   /**
     * is set hash
     *
     * @return bool
     */
    public function isSetUserHash($aHash)
    {
        return $this->getDbTable()->getByHashcode($aHash);
    }


    /**
     * Get filter
     *
     * @param array $aParams
     * @return array
     */
    public function getFilter($aParams)
    {
        switch($aParams['filter']) {
            case 'to all':
                $filter = '1';
                break;
            case 'to all active':
                $filter = 'status = "'.Users_Model_User::STATUS_ACTIVE.'"';
                break;
            case 'to all disabled':
                $filter = 'status = "'.Users_Model_User::STATUS_BLOCKED.'"';
                break;
            case 'to all not active last month':
                $filter = 'logined < DATE_SUB(NOW(), INTERVAL 1 MONTH)';
                break;
            case 'custom email':
                $filterInput = (isset($aParams['filterInput']))
                    ? $aParams['filterInput'] : "";
                preg_match_all(
                    '/[\S]+\@[\S]+\.\w+/',
                    $filterInput,
                    $matches
                );
                $filter = 'email in ("'.join('","', $matches['0']).'")';
                break;
            default:
                throw new Exception('no such filter ' . $aParams['filter']);
                break;

        }

        $select = $this->getDbTable()->select()
                       ->from(array('users'), array('email', 'login'))
                       ->where($filter);

        if (!$aParams['ignore']) {
            $select->where('inform=?', 'true');
        }

        return $this->getDbTable()->fetchAll($select)->toArray();
    }

    private function _getIpFromRequest()
    {
        return (!empty($_SERVER["REMOTE_ADDR"]))
            ? $_SERVER["REMOTE_ADDR"]
            : "0.0.0.0";
    }
}
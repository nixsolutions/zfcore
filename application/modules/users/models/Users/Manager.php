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
            $authData = $authAdapter->getResultRowObject(
                null,
                array('password')
            );
            $auth->getStorage()->write($authData);
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
            $user->ip      = $_SERVER['REMOTE_ADDR'];
            $user->count++;
            $user->save();
            
            if ($data['remember']) {
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
                'ip'       => $_SERVER['REMOTE_ADDR']
            )
        );
        $user = $this->getDbTable()->create($data);
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

        if ($user->id) {
            $user->hashCode = null;
            $user->status   = Users_Model_User::STATUS_ACTIVE;
            $user->save();
            
            return true;
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
        if ($user->id) {
            $user->hashCode = md5($user->login . uniqid());
            $user->save();
            return $user;
        }
        return false;
    }
    
    /**
     * Forget password confirmation
     *
     * @return bool|new password
     */
    public function forgetPasswordConfirm($aHash, $aPassword = null)
    {
        $user = $this->getDbTable()->getByHashcode($aHash);
        if ($user->id) {
            if ($aPassword) { //confirm to change password
                 $user->password = $aPassword;
                 $user->hashCode = null;
                 $user->save();
                 return $user;
            } else { //else don't want to change the password
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
                preg_match_all(
                    '/[\S]+\@[\S]+\.\w+/',
                    $aParams['filterInput'],
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
}
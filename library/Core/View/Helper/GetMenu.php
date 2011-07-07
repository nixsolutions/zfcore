<?php
/**
 * @see Zend_View_Helper_Navigation_HelperAbstract
 */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * Frontend navigation menu helper based on database
 *
 * @category    Core
 * @package     Core_View
 * @subpackage  Helper
 *
 * @author      Valeriu Baleyko <baleyko.v.v@gmail.com>
 * @copyright   NIX Solutions (http://www.nixolutions.com/)
 */
class Core_View_Helper_GetMenu
    extends Zend_View_Helper_Navigation_Menu
{
    protected static $_menuTree = null;

    /**
     * Constructor
     *
     * @return Core_View_Helper_GetMenu
     */
    public function __construct ()
    {
        if (null === self::$_menuTree) {
           self::$_menuTree = new Menus_Model_Menu_Manager();
        }

    }

    /**
     * getMenu
     *
     * @param integer|string $labelOrId
     * @return object Core_View_Helper_GetMenu
     */
    public function getMenu($labelOrId = null)
    {
        if (is_integer($labelOrId)) {
            $menuArray = self::$_menuTree->getMenuById((int) $labelOrId);
            $menuArray = $menuArray['pages'];
        } else if (is_string($labelOrId)) {
           $menuArray = self::$_menuTree->getMenuByLabel((string) $labelOrId);
            $menuArray = $menuArray['pages'];
        } else {
            $menuArray = array();
        }

       $acl = Zend_Registry::get('Acl');
       $identity = Zend_Auth::getInstance()->getIdentity();
        if ($identity) {
            $role = $identity->role;
        } else {
            $role = 'guest';
        }
        $this->setAcl($acl)->setRole($role);

        $container = new Zend_Navigation($menuArray);
        $iterator = new RecursiveIteratorIterator($container, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $page) {
            $resourceName  = 'mvc:';

            if (!empty($page->module)) {
                $resourceName .= $page->module . '/';

                 if (!empty($page->controller)) {
                    $resourceName .= $page->controller;
                 } else {
                     $resourceName .= 'index';
                 }

                 if (!empty($page->action)) {
                    $action = $page->action;

                 } else {
                     $action = 'index';
                 }

                 try {

                        if (!$this->getAcl()->isAllowed(
                            'guest',
                            $resourceName,
                            $action
                            )){
                                $page->visible = 0;

                            }

                }  catch (Exception $e) {
                           // $page->visible = 0;
                }
            }
        }

        $this->setContainer($container);

        return parent::menu();
    }
}

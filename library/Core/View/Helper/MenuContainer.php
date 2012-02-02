<?php
/**
 * Copyright (c) 2012 by PHP Team of NIX Solutions Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
/**
 *
 * @category   Core
 * @package    Core_View
 * @subpackage Helper
 */
class Core_View_Helper_MenuContainer
{
    /**
    * Identifier of menu
    */
    private $_identifier = 0;

    /**
    * Identifier of menu
    */
    private $_section = 'default';

    /*
     * Constructor menuContainer
     */
    public function menuContainer($identifier = 0)
    {
        if ($identifier !== 0) {
            $this->_identifier = $identifier;
        } else {
            return $this;
        }

        $this->_section = $this->getSection();

        $source = $this->getSource();

        if ($source == 'db') {
            $menuArray = $this->getMenuArrayByDb();
        } else {
            $menuArray = $this->getMenuArrayByConfig();
        }

        return $this->getContainerByArray($menuArray);

    }

    /**
     * Get container by array
     *
     * @param array $menuArray
     * @return Zend_Navigation_Container
     */
    public function getContainerByArray($menuArray)
    {
        $container = new Zend_Navigation(array($menuArray));
        $view = Zend_Layout::getMvcInstance()->getView();
        $view->setScriptPath(APPLICATION_PATH . '/layouts/scripts');

        $acl = Zend_Registry::get('Acl');
        $identity = Zend_Auth::getInstance()->getIdentity();
        if ($identity) {
            $role = $identity->role;
        } else {
            $role = 'guest';
        }
        $view->navigation()->setAcl($acl)->setRole($role);

        if (Zend_Registry::isRegistered('Zend_Translate')) {
            $view->navigation()->setTranslator(Zend_Registry::get('Zend_Translate'));
        }

        $iterator = new RecursiveIteratorIterator($container, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $page) {

            $resourceName = 'mvc:';

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
                    if (!$view->navigation()->getAcl()->isAllowed(
                        $role,
                        $resourceName,
                        $action
                    )) {
                        $page->visible = 0;
                    }

                } catch (Exception $e) {
                    // $page->visible = 0;
                }
            }
        }
        return $container;
    }

    /**
     * Get layout section
     * @return string
     */
    public function getSection()
    {
        $currentLayout = Zend_Layout::getMvcInstance()->getLayout();
        $currentLayout = preg_split('/\//', $currentLayout);
        return $currentLayout[0];
    }

    /**
     * Get source from config
     * @return string
     */
    public function getSource()
    {
        $config = Core_Module_Config::getConfig('application');
        if (APPLICATION_ENV == 'testing') {
            $source = $config['testing']['resources']['navigation']['source'];
        } else {
            $source = $config['production']['resources']['navigation']['source'];
        }

        if (isset($source[$this->_section])) {
            return $source[$this->_section];
        } else {
            return null;
        }

    }

    /**
     * Get menu array by config
     *
     * @return array
     */
    public function getMenuArrayByConfig()
    {
        if (Zend_Registry::isRegistered('menuArray')) {
            $dataArray = Zend_Registry::get('menuArray');
        }
        $root = array(
            'label'   => 'Home',
            'id'      => 0,
            'type'    => 'uri',
            'uri'   => '/',
            'visible' => true,
            'active' => false
        );
        if (isset($dataArray[$this->_section])) {
            $root['pages'] = $dataArray[$this->_section];
            $menuArray = $this->getArrayItemByKey($root, 'label', $this->_identifier);
            if (is_null($menuArray)) {
                $menuArray = $root;
            }
            return $menuArray;
        } else {
            return $root;
        }

    }

    /**
    * Get menu array by DB
    *
    * @return array
    */
    public function getMenuArrayByDb()
    {
        $menuManager = new Menu_Model_Menu_Manager();
        if (is_string($this->_identifier)) {
            $menuArray = $menuManager->getMenuByLabel($this->_identifier);
        } elseif (is_integer($this->_identifier)) {
            $menuArray = $menuManager->getMenuById($this->_identifier);
        } else {
            $menuArray = $menuManager->getMenuById(0);
        }
        return $menuArray;
    }

    /**
     * get array item by key
     * @param array $arr
     * @param string $name
     * @param string $value
     * @return array
     */
    public function getArrayItemByKey($arr, $name, $value)
    {
        if (!is_array($arr)) {
            return null;
        }

        if (isset($arr[$name]) && $arr[$name] == $value) {
            $arr['label'] = null;
            return $arr;
        }

        if (!isset($arr['pages'])) {
            return null;
        }

        foreach ($arr['pages'] as $page) {
            $results = $this->getArrayItemByKey($page, $name, $value);
            if (null !== $results) {
                return $results;
            }
        }
        return null;
    }

}

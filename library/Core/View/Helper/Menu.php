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
class Core_View_Helper_Menu
{
    /**
    * Identifier of menu
    */
    private $_identifier = 0;

    /*
     * Constructor menu
     */
    public function menu($identifier = 0, $minDepth = 0, $maxDepth = 50, $partial = null, $type = null)
    {
        if ($identifier !== 0) {
            $this->_identifier = $identifier;
        } else {
            return $this;
        }

        $view = new Zend_View();
        $view->setScriptPath(APPLICATION_PATH . '/layouts/scripts');

        $menuManager = new Menu_Model_Menu_Manager();
        if (is_string($this->_identifier)) {
            $menuArray = $menuManager->getMenuByLabel($this->_identifier);
        } elseif (is_integer($this->_identifier)) {
            $menuArray = $menuManager->getMenuById($this->_identifier);
        } else {
            $menuArray = $menuManager->getMenuById(0);
        }
        if (is_null($menuArray)) {
            return false;
        }

        $container = new Zend_Navigation(array($menuArray));

        $acl = Zend_Registry::get('Acl');
        $identity = Zend_Auth::getInstance()->getIdentity();
        if ($identity) {
            $role = $identity->role;
        } else {
            $role = 'guest';
        }
        $view->navigation()->setAcl($acl)->setRole($role);

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
                    )
                    ) {
                        $page->visible = 0;
                    }

                } catch (Exception $e) {
                    // $page->visible = 0;
                }
            }
        }

        if ($type == 'breadcrumbs') {
            return $view->navigation()
                ->breadcrumbs($container)
                ->setPartial($partial)
                ->setMaxDepth($maxDepth)
                ->setPartial($partial);
        }

        return $view->navigation()
            ->menu($container)
            ->setPartial($partial)
            ->setMaxDepth($maxDepth)
            ->setMinDepth($minDepth);
    }

}

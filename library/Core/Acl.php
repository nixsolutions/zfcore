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
 * Config support
 *
 * @category Core
 * @package  Core_Acl
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 */
class Core_Acl extends Zend_Acl
{
    /**
     * Constants for sets permissions
     */
    const ALLOW = 'allow';
    const DENY = 'deny';

    /**
     * Creates a new navigation container
     *
     * @param array|Zend_Config $config    [optional] rules to add
     * @throws Zend_Navigation_Exception  if $pages is invalid
     */
    public function __construct($config = null)
    {
        if (is_array( $config ) || $config instanceof Zend_Config) {
            $this->_build( $config );
        } elseif (null !== $config) {
            throw new Zend_Acl_Exception(
                'Invalid argument: $config must be an array, an ' .
                    'instance of Zend_Config, or null');
        }
    }

    /**
     * Add rules to Acl
     *
     * @param array|Zend_Config $config
     * @return Core_Acl
     */
    protected function _build($config)
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }
        if (isset($config['roles']))
            $this->_addRoles( $config['roles'] );


        if (isset($config['mvc']))
            $this->_addMvcResources( $config['mvc'] );

        if (isset($config['resources']))
            $this->_addResources( $config['resources'] );

        if (isset($config['rules']))
            $this->_addRules( $config['rules'] );

        return $this;
    }

    /**
     * Add roles
     *
     * @param array $roles
     * @return Core_Acl
     */
    protected function _addRoles($roles)
    {
        foreach ($roles as $name => $parents) {
            if (!$this->hasRole( $name )) {
                if (empty($parents)) {
                    $parents = null;
                } else {
                    if (!is_array( $parents )) {
                        $parents = null;
                    }
                }
                $this->addRole( new Zend_Acl_Role($name), $parents );
            }
        }
        return $this;
    }

    /**
     * Add resources
     *
     * @param array $resources
     * @return Core_Acl
     */
    protected function _addResources($resources)
    {
        // modules loop
        foreach ((array)$resources as $resource => $parent) {
            if (empty($parent['parent'])) {
                $parent = null;
            } else {
                $parent = $parent['parent'];
            }

            if (!$this->has( $resource )) {
                $this->addResource( new Zend_Acl_Resource($resource), $parent );
            }
        }

        return $this;
    }

    /**
     * Add resources
     *
     * @param array $resources
     * @return Core_Acl
     */
    protected function _addMvcResources($resources)
    {
        $prefix = 'mvc:';

        // modules loop        
        foreach ((array)$resources as $module => $controllers) {
            foreach ((array)$controllers as $controller => $actions) {
                // resource name, like "mvc:users/login"
                $resource = $prefix . $module . '/' . $controller;
                // add new resource
                if (!$this->has( $resource )) {
                    $this->addResource( new Zend_Acl_Resource($resource) );
                }

                if (is_array( $actions )) {
                    $this->_addMvcRules( $actions, $resource );
                } else {
                    // not set allow/deny - then allow to guest
                    $this->allow( 'guest', $resource );
                }
            }
        }
        return $this;
    }

    /**
     * _addMvcRules
     *
     * setup allow/deny rules
     * <code>
     * $this->_addMvcRules(
     *   array(
     *      'deny'  => 'guest,user',
     *      'allow' => 'admin,superadmin',
     *      'action1' => array('allow'=>...,'deny'=>...),
     *      'action2' => array('allow'=>...,'deny'=>...)
     *   ),
     *   'mvc:module/controller',
     *   'action'
     * );
     * </code>
     *
     * @param   array  $rules  array
     * @param   string $resource
     * @param   string $privileges
     * @return  array
     */
    protected function _addMvcRules($rules, $resource, $privileges = null)
    {
        if (isset($rules[self::ALLOW])) {
            if (is_string( $rules[self::ALLOW] )) {
                $rules[self::ALLOW] = preg_split( '/,/', $rules[self::ALLOW] );
            }
        } else {
            $rules[self::ALLOW] = array();
        }

        if (isset($rules[self::DENY])) {
            if (is_string( $rules[self::DENY] )) {
                $rules[self::DENY] = preg_split( '/,/', $rules[self::DENY] );
            }
        } else {
            $rules[self::DENY] = array();
        }
        foreach ($rules[self::ALLOW] as $role) {
            $this->allow( $role, $resource, $privileges );
        }
        unset($rules[self::ALLOW]);

        foreach ($rules[self::DENY] as $role) {
            $this->deny( $role, $resource, $privileges );
        }
        unset($rules[self::DENY]);

        if (sizeof( $rules ) > 0) {
            foreach ($rules as $privileges => $rules) {
                $this->_addMvcRules( $rules, $resource, $privileges );
            }
        }

        return $this;
    }

    /**
     * _addRules
     *
     * add new rules to registry
     *
     * @param   array $rules
     * @return  Core_Acl  $this
     */
    protected function _addRules($rules)
    {
        unset($rules[0]);
        foreach ($rules as $type => $options) {
            if (isset($options['resource'])) {
                $this->_addRule( $type, $options );
            } else {
                foreach ($options as $rule) {
                    $this->_addRule( $type, $rule );
                }
            }
        }
        return $this;
    }


    /**
     * _addRule
     *
     * add new rule to registry
     *
     * @param   array $type allow|deny
     * @param   array $options
     * @return  Core_Acl  $this
     */
    protected function _addRule($type, $options)
    {
        if (isset($options['resource'])) {
            if (isset($options['privilege']) && !empty($options['privilege'])) {
                $privilege = $options['privilege'];
            } else {
                // for all privilege
                $privilege = null;
            }

            if (isset($options['role']) && !empty($options['role'])) {
                $role = $options['role'];
            } else {
                // for all roles
                $role = null;
            }

            if ($this->has( $options['resource'] )) {
                $this->{$type}( $role,
                    $options['resource'],
                    $privilege );
            }
        }
        return $this;
    }
}  
<?php

/**
 * Class Core_Controller_NotFoundException
 *
 *
 * @category Core
 * @package  Core_Controller
 *
 * @uses     Zend_Controller_Action_Exception
 */
class Core_Controller_NotFoundException extends Zend_Controller_Action_Exception
{
    protected $code = 404;
}
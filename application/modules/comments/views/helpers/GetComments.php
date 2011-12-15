<?php
/**
 * Comments_View_Helper_GetComments
 *
 * @version $Id$
 */
class Comments_View_Helper_GetComments extends Zend_View_Helper_Abstract
{
    public function getComments($alias)
    {
        return $this->view->action(
            'index', 
            'index', 
            'comments', 
            array(
                'alias' => $alias,
                'returnUrl' => $this->view->url()
            )
        );
    }
}
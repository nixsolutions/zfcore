<?php
/**
 * Blog_View_Helper_Date
 *
 * @version $Id$
 */
class Blog_View_Helper_Date extends Zend_View_Helper_Abstract
{
    public function date($date, $format = 'F j, Y')
    {
        return date($format, strtotime($date));
    }
}
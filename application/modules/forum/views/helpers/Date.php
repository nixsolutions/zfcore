<?php
/**
 * Forum_View_Helper_Date
 *
 * @version $Id$
 */
class Forum_View_Helper_Date extends Zend_View_Helper_Abstract
{
    public function date($date, $format = 'H:i F j, Y')
    {
        return date($format, strtotime($date));
    }
}
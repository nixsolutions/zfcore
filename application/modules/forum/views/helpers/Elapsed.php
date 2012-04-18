<?php
/**
 * Forum_View_Helper_Elapsed
 */
class Forum_View_Helper_Elapsed extends Zend_View_Helper_Abstract
{
    public function elapsed($date)
    {
        $time =  strtotime($date);

        if ($time < 999999) {
            return $this->view->__("never");
        }

        $time = abs(time() - $time); // to get the time since that moment

        $tokens = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        /**
         * @todo use plural forms
         * @link http://framework.zend.com/manual/en/zend.translate.plurals.html
         */
        $_units = array(
            'year'   => array('year', 'years'),
            'month'  => array('month', 'months'),
            'week'   => array('week', 'weeks'),
            'day'    => array('day', 'days'),
            'hour'   => array('hour', 'hours'),
            'minute' => array('minute', 'minutes'),
            'second' => array('second', 'seconds'),
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit)
                continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits . ' ' . (($numberOfUnits > 1) ? $_units[$text][1] : $_units[$text][0]);
        }

        return $this->view->__("never");
    }
}
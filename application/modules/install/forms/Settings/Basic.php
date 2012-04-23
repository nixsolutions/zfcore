<?php
/**
 * Database form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Install_Form_Settings_Basic extends Core_Form
{
    /**
     * Form initialization
     *
     * @return Install_Form_Settings_Basic
     */
    public function init()
    {
        $this->setName('settingsForm');
        $this->setMethod('post');

        $this->addElement($this->_title());

        $this->addElement($this->_baseUrl());

        $this->addElement($this->_timezone());

        $this->addElement($this->_submit());

        return $this;
    }

    protected function _baseUrl()
    {
        $element = new Zend_Form_Element_Text('baseUrl');
        $element->setLabel('Base Url')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span4')
                ->setValue('/');

        return $element;
    }

    protected function _timezone()
    {
        $element = new Zend_Form_Element_Select('timezone');
        $element->setLabel('Timezone')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span4')
        ;

        $timezones = array();
        $timezoneIdentifiers = DateTimeZone::listIdentifiers();

        foreach ($timezoneIdentifiers as $timezone) {
            if (preg_match('/^(Africa|America|Antarctica|Asia|Atlantic|Europe|Indian|Pacific)\//', $timezone)) {
                $ex = explode('/', $timezone);
                $city = isset($ex[2]) ? $ex[1] . ' - ' . $ex[2] : $ex[1];
                $name = $ex[0];
                $timezones[$name][$timezone] = $city;

                $dateTimeZoneGmt = new DateTimeZone('GMT');
                $dateTimeZone = new DateTimeZone($timezone);

                $dateTimeGmt = new DateTime("now", $dateTimeZoneGmt);
                $timeOffset = $dateTimeZone->getOffset($dateTimeGmt);

                $gmt = $timeOffset/3600;
                if ($gmt == 0) {
                    $gmt = ' 00';
                } elseif ($gmt > 0 && $gmt < 10) {
                    $gmt = '+0' . $gmt;
                } elseif ($gmt >= 10) {
                    $gmt = '+' . $gmt;
                } elseif ($gmt < 0 && $gmt > -10) {
                    $gmt = '-0' . abs($gmt);
                }

                $timezones[$name][$timezone] = substr($timezone, strlen($name) + 1) . ' (GMT ' . $gmt . ':00)';
            }
        }
        $element->addMultiOptions($timezones);

        return $element;
    }

    protected function _title()
    {
        $element = new Zend_Form_Element_Text('title');
        $element->setLabel('Sitename')
                ->addDecorators($this->_decorators)
                ->setRequired(true)
                ->setAttrib('class', 'span4')
                ->setValue('My ZFCore Site');

        return $element;
    }

    public function isWritable($path)
    {
        $path = APPLICATION_PATH . '/../public/' . $path;
        return is_dir($path) && is_writable($path);
    }
}
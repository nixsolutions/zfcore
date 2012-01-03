<?php
/**
 * Database form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 *
 * @version  $Id$
 */
class Install_Form_Settings_Basic extends Zend_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName('settingsForm');
        $this->setMethod('post');

        $this->addElement($this->_baseUrl());

        $this->addElement($this->_timezone());

        $this->addElement($this->_title());

        $this->addElement($this->_submit());

        return $this;
    }

    protected function _submit()
    {
        $sudmit = new Zend_Form_Element_Submit('submit');
        $sudmit->setLabel('Save & Next >');
        return $sudmit;
    }

    protected function _baseUrl()
    {
        $element = new Zend_Form_Element_Text('baseUrl');
        $element->setLabel('Base Url');
        $element->setRequired(true)->setAttrib('style', 'width:100%');
        $element->setValue('/');

        return $element;
    }

    protected function _timezone()
    {
        $element = new Zend_Form_Element_Select('timezone');
        $element->setLabel('Timezone');
        $element->setRequired(true)->setAttrib('style', 'width:100%');

        $regions = array(
            'Africa' => DateTimeZone::AFRICA,
            'America' => DateTimeZone::AMERICA,
            'Antarctica' => DateTimeZone::ANTARCTICA,
            'Aisa' => DateTimeZone::ASIA,
            'Atlantic' => DateTimeZone::ATLANTIC,
            'Europe' => DateTimeZone::EUROPE,
            'Indian' => DateTimeZone::INDIAN,
            'Pacific' => DateTimeZone::PACIFIC
        );
        $timezones = array();
        foreach ($regions as $name => $mask) {
            $zones = DateTimeZone::listIdentifiers($mask);
            foreach ($zones as $timezone) {
                $dateTimeZoneGmt = new DateTimeZone('GMT');
                $dateTimeZone = new DateTimeZone($timezone);

                $dateTimeGmt = new DateTime("now", $dateTimeZoneGmt);
                $timeOffset = $dateTimeZone->getOffset($dateTimeGmt);
                $gmt = $timeOffset/3600;

                if ($gmt == 0) {
                    $gmt = ' 00';
                } elseif($gmt > 0 && $gmt < 10) {
                    $gmt = '+0' . $gmt;
                } elseif($gmt >= 10) {
                    $gmt = '+' . $gmt;
                } elseif($gmt < 0 && $gmt > -10) {
                    $gmt = '-0' . abs($gmt);
                } elseif($gmt <= -10) {
                    $gmt = $gmt;
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
        $element->setLabel('Sitename');
        $element->setRequired(true)->setAttrib('style', 'width:100%');

        $element->setValue('My ZFCore Site');

        return $element;
    }

    public function isWritable($path)
    {
        $path = APPLICATION_PATH . '/../public/' . $path;
        return is_dir($path) && is_writable($path);
    }
}
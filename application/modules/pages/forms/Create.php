<?php
/**
 * Edit page form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Pages_Form_Create extends Zend_Form
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title:')
            ->setRequired(true)
            ->addValidator('regex', false, array('/^[\w\s\'",.\-_]+$/i', 'messages' => array(
                Zend_Validate_Regex::INVALID => 'Invalid title',
                Zend_Validate_Regex::NOT_MATCH => 'Invalid title'
            )));

        $alias = new Zend_Form_Element_Text('alias');
        $alias->setLabel('Alias(permalink):')
            ->setRequired(true)
            ->addValidator('regex', false, array('/^[a-z0-9\-\_]+$/i', 'messages' => array(
                Zend_Validate_Regex::INVALID => 'Invalid page alias',
                Zend_Validate_Regex::NOT_MATCH => 'Invalid page alias'
            )));

        $content = new Core_Form_Element_Redactor('content');
        $content->setLabel('Content:')
            ->setRequired(true)
            ->setAttrib('cols', 50)
            ->setAttrib('rows', 50)
            ->setAttrib('redactor', array(
                'toolbar' => 'full',
                'image_upload' => $this->_getUploadImageUrl()
            ));

        $keywords = new Zend_Form_Element_Text('keywords');
        $keywords->setLabel('Keywords:')
            ->setRequired(true)
            ->addValidator('regex', false, array('/^[\w\s\,\.\-\_]+$/i', 'messages' => array(
                Zend_Validate_Regex::INVALID => 'Invalid meta keywords',
                Zend_Validate_Regex::NOT_MATCH => 'Invalid meta keywords'
            )));

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description:')
            ->setRequired(true)
            ->addValidator('regex', false, array('/^[\w\s\,\.\-\_]+$/i', 'messages' => array(
                Zend_Validate_Regex::INVALID => 'Invalid meta description',
                Zend_Validate_Regex::NOT_MATCH => 'Invalid meta description'
            )));

        $pid = new Zend_Form_Element_Hidden('pid');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Create');

        $this->addElements(array(
            $title, $alias, $content, $keywords, $description, $submit, $pid
        ));
    }

    /**
     * get upload image url
     *
     * @return string
     */
    protected function _getUploadImageUrl()
    {
        return Zend_Controller_Front::getInstance()->getParam('bootstrap')
                                                   ->getOption('uploadDir');
    }
}

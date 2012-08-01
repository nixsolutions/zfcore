<?php
/**
 * Edit page form
 *
 * @category Application
 * @package Model
 * @subpackage Form
 */
class Pages_Form_Create extends Core_Form
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
            ->addDecorators($this->_inputDecorators)
            ->addValidator(
                'regex',
                false,
                array(
                    '/^[\w\s\'",.\-_]+$/i',
                    'messages' => array(
                        Zend_Validate_Regex::INVALID => 'Invalid title',
                        Zend_Validate_Regex::NOT_MATCH => 'Invalid title'
                    )
                )
            );
        $title->setAttrib('class', 'span4');

        $alias = new Zend_Form_Element_Text('alias');
        $alias->setLabel('Alias(permalink):')
            ->addDecorators($this->_inputDecorators)
            ->setRequired(true)
            ->addValidator(
                'regex',
                false,
                array(
                    '/^[a-z0-9\-\_]+$/i',
                    'messages' => array(
                        Zend_Validate_Regex::INVALID => 'Invalid page alias',
                        Zend_Validate_Regex::NOT_MATCH => 'Invalid page alias'
                    )
                )
            );
        $alias->setAttrib('class', 'span4');

        $content = new Core_Form_Element_Redactor(
            'content', array(
                'label' => 'Content:',
                'cols'  => 50,
                'rows'  => 25,
                'required' => true,
                'filters' => array('StringTrim'),
                'redactor' => array(
                   'imageUpload'  => '/pages/images/upload/', // url or false
                   'imageGetJson' => '/pages/images/list/',
                   'fileUpload'   => '/admin/files/upload/',
                   'fileDownload' => '/admin/files/download/?file=',
                   'fileDelete'   => '/admin/files/delete/?file=',
                ))
        );

        $content->setRequired(true);

        $keywords = new Zend_Form_Element_Text('keywords');
        $keywords->setLabel('Keywords:')
                 ->setRequired(true)
                 ->addDecorators($this->_inputDecorators)
                 ->addValidator(
                     'regex', false, array(
                         '/^[\w\s\,\.\-\_]+$/i', 'messages' => array(
                            Zend_Validate_Regex::INVALID => 'Invalid meta keywords',
                            Zend_Validate_Regex::NOT_MATCH => 'Invalid meta keywords'
                         )
                     )
                 );
        $keywords->setAttrib('class', 'span6');

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description:')
                    ->setRequired(true)
                    ->addDecorators($this->_inputDecorators)
                    ->addValidator(
                        'regex', false, array(
                            '/^[\w\s\,\.\-\_]+$/i',
                            'messages' => array(
                                Zend_Validate_Regex::INVALID => 'Invalid meta description',
                                Zend_Validate_Regex::NOT_MATCH => 'Invalid meta description'
                            )
                        )
                    );

        $description->setAttrib('class', 'span6');
        $pid = new Zend_Form_Element_Hidden('pid');

        $this->addElements(
            array(
                $title, $alias, $content, $keywords, $description, $this->_submit(), $pid
            )
        );
    }
}

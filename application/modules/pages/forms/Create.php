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
    protected $_inputDecorators = array(
        array('HtmlTag', array('tag' => 'dd', 'class'=>'control-group')),
        array('Label', array('tag' => 'dt', 'class'=>'control-group')),
        array('Errors', array('class'=>'help-inline')),
    );

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

        $content = new Core_Form_Element_Redactor('content', array(
           'label' => 'Content:',
           'cols'  => 50,
           'rows'  => 25,
           'required' => true,
           'filters' => array('StringTrim'),
           'redactor' => array(
               'imageUpload'  => '/pages/images/upload/', // url or false
               'fileUpload'   => '/pages/files/upload/',
               'fileDownload' => '/pages/files/download/?file=',
               'fileDelete'   => '/pages/files/delete/?file=',
           )
        ));
        $content->addDecorators($this->_inputDecorators);

        $keywords = new Zend_Form_Element_Text('keywords');
        $keywords->setLabel('Keywords:')
                 ->addDecorators($this->_inputDecorators)
                 ->setRequired(true)
                 ->addValidator(
                     'regex', false, array(
                         '/^[\w\s\,\.\-\_]+$/i', 'messages' => array(
                            Zend_Validate_Regex::INVALID => 'Invalid meta keywords',
                            Zend_Validate_Regex::NOT_MATCH => 'Invalid meta keywords'
                         )
                     )
                 );

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description:')
                    ->addDecorators($this->_inputDecorators)
                    ->setRequired(true)
                    ->addValidator(
                        'regex', false, array(
                            '/^[\w\s\,\.\-\_]+$/i',
                            'messages' => array(
                                Zend_Validate_Regex::INVALID => 'Invalid meta description',
                                Zend_Validate_Regex::NOT_MATCH => 'Invalid meta description'
                            )
                        )
                    );

        $pid = new Zend_Form_Element_Hidden('pid');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Create');
        $submit->setAttrib('class','btn btn-primary');

        $this->addElements(
            array(
                $title, $alias, $content, $keywords, $description, $submit, $pid
            )
        );
    }
}

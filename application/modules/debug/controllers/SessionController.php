<?php
/**
 * SessionController for debug module
 *
 * @category   Application
 * @package    Debug
 * @subpackage Controller
 *
 * @author Anna Pavlova <pavlova.anna@nixsolutions.com>
 *
 * @version  $Id$
 */


class Debug_SessionController extends Core_Controller_Action_Crud
{
    /**
     * _prepareGrid
     *
     * @return Debug_CrontabController
     */
    protected function _prepareGrid()
    {
        $this->_grid
            ->setColumn(
                    'id',
                    array(
                        'name' => 'Id',
                        'type' => Core_Grid::TYPE_DATA,
                        'index' => 'id'
                    )
                )
            ->setColumn(
                'value',
                array(
                    'name' => 'Value',
                    'type' => Core_Grid::TYPE_DATA,
                    'index' => 'value',
                    'formatter' => array($this, 'formatter')
                )
            )
        ;
        $this->_addShowColumn();
        $this->_addDeleteColumn();
    }

    /**
     * get source
     *
     * @return Core_Grid_Adapter_AdapterInterface
     */
    protected function _getSource()
    {
        $manager = new Debug_Model_Session_Manager();
        return new Core_Grid_Adapter_Array($manager->createSessionArray());
    }

    /**
     * get table
     *
     * @return void
     */
    protected function _getTable()
    {
    }


    /**
     * Add a "show" column to the grid
     *
     * @return void
     */
    public function _addShowColumn()
    {
        $this->_grid->setColumn(
            'show',
            array(
                'name' => 'Show',
                'formatter' => array($this, 'showFormatter'),
                'attribs'   => array('width' => '60px')
            )
        );
    }

    /**
     * Formatter for the "show" column value
     *
     * @param $value
     * @param $row
     * @return string
     */
    public function showFormatter($value, $row)
    {
        $link = '<a href="%s" class="btn dialog">Show</a>';
        $url = $this->getHelper('url')->url(
            array(
                'controller' => 'session',
                'action' => 'show',
                'key' => $row['id']
            ),
            'default'
        );

        return sprintf($link, $url);
    }

    /**
     * formatter
     *
     * @param $value
     * @param $row
     * @return string
     */
    public function formatter($value, $row)
    {
        return "<pre style='max-height: 160px; overflow: auto;'>{$value}</pre>";
    }


    /**
     * _getCreateForm
     *
     * return create form for scaffolding
     *
     * @return  Zend_Form
     */
    protected function _getCreateForm()
    {
        return new Debug_Model_Session_Form_Create();
    }

    /**
     * _getEditForm
     *
     * return edit form for scaffolding
     *
     * @return  Zend_Form
     */
    protected function _getEditForm()
    {
    }

    /**
     * showAction
     *
     * @return void
     */
    function showAction()
    {
        $key = $this->_getParam('key');
        $this->view->namespace = new Zend_Session_Namespace($key);
        $this->_helper->layout()->disableLayout();
    }

    /**
     * createAction
     *
     * create page instance
     *
     * @return  void
     */
    public function createAction()
    {
        $manager = new Debug_Model_Session_Manager();
        $createForm = $this->_getCreateForm()
                           ->setAction($this->view->url());

        if ($this->_request->isPost() &&
            $createForm->isValid($this->_getAllParams())) {
            $form = $createForm->getValues();
            if ($manager->createSession($form)) {
                $this->_flashMessenger->addMessage('Successfully!');
            } else {
                $this->_flashMessenger->addMessage('Failed!');
            }

            $this->_helper->getHelper('redirector')->direct('index');
        }
        $this->view->form = $createForm;
    }

    /**
     * deleteAction
     *
     * delete variable from Session
     *
     * @return  json
     */
    public function deleteAction()
    {
        $manager = new Debug_Model_Session_Manager();
        $this->_helper->json($manager->deleteSession($this->_getParam('id')));
    }
}


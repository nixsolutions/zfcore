<?php
/**
 * Copyright (c) 2012 by PHP Team of NIX Solutions Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Core_Controller_Action_Crud
 *
 * @uses       Zend_Controller_Action
 * @category   Core
 * @package    Core_Controller
 * @subpackage Action
 */
abstract class Core_Controller_Action_Crud extends Core_Controller_Action
{

    /**
     * @var Core_Grid
     */
    protected $_grid;

    protected $_limit = 15;

    /**
     * @var array
     */
    protected $_orders = array();

    /**
     * @var array
     */
    protected $_filters = array();

    /**
     * init controller
     *
     * @return void
     */
    public function init()
    {
        $this->_useDashboard();

        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->_viewRenderer = $this->_helper->getHelper('viewRenderer');
        $this->_redirector = $this->_helper->getHelper('redirector');
    }

    /**
     * index
     *
     * @return void
     */
    public function indexAction()
    {
        /**
         * todo: do it better way
         * init grid before rendering, catch all exception in action
         */
        $this->_changeViewScriptPathSpec();
        $this->_prepareHeader();

        $this->view->filters = $this->_filters;
    }

    /**
     * grid
     *
     * @return void
     */
    public function gridAction()
    {
        $this->_changeViewScriptPathSpec();
        $this->_loadGrid();
        $this->_prepareGrid();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }

        /**
         * todo: do it better way
         * init grid before rendering, catch all exception in action
         */
        $this->_grid->getHeaders();
        $this->_grid->getData();
        $this->view->grid = $this->_grid;
    }

    /**
     * create
     *
     * @return void
     */
    public function createAction()
    {
        $this->_changeViewScriptPathSpec();

        $table = $this->_getTable();
        $form = $this->_getCreateForm()
            ->setAction($this->view->url());

        if ($this->_request->isPost() &&
            $form->isValid($this->_getAllParams())
        ) {
            // create record in DB
            $table->createRow($form->getValues())
                ->save();

            $this->_helper->flashMessenger('Successfully');
            $this->_helper->redirector('index');
        } elseif ($this->_request->isPost()) {
            // show errors
            $errors = $form->getErrors();
            foreach ($errors as $fn => $error) {
                if (empty($error)) continue;
                $el = $form->getElement($fn);
                $dec = $el->getDecorator('HtmlTag');
                $cls = $dec->getOption('class');
                $dec->setOption('class', $cls .' error');
            }
        }

        $this->view->form = $form;
    }

    /**
     * edit
     *
     * @return void
     */
    public function editAction()
    {
        $this->_changeViewScriptPathSpec();

        $model = $this->_loadModel();

        $form = $this->_getEditForm()
            ->setAction($this->view->url())
            ->setDefaults($model->toArray());

        if ($this->_request->isPost() &&
            $form->isValid($this->_getAllParams())
        ) {
            $model->setFromArray($form->getValues())
                ->save();

            $this->_helper->flashMessenger('Record was updated');
            $this->_helper->redirector('index');
        }

        $this->view->form = $form;
    }

    /**
     * load model
     *
     * @return Zend_Db_Table_Row_Abstract
     */
    protected function _loadModel()
    {
        if (!$id = $this->_getParam('id')) {
            $this->_forwardNotFound();
        }

        $table = $this->_getTable();

        if (!$model = $table->getById($id)) {
            $this->_forwardNotFound();
        }

        return $model;
    }

    /**
     * change view script path specification
     *
     * @return void
     */
    protected function _changeViewScriptPathSpec()
    {
        $this->_viewRenderer->setViewScriptPathSpec('crud/:action.:suffix');
    }

    /**
     * delete
     *
     * @return void
     */
    public function deleteAction()
    {
        $model = $this->_loadModel();

        if ($model) {
            $this->_helper->json($model->delete());
        } else {
            $this->_helper->json(false);
        }
    }

    /**
     * @return void
     */
    public function deleteAllAction()
    {
        $res = false;
        if ($ids = $this->_getParam('ids')) {
            $this->_getTable()->delete(
                $this->_getTable()->getAdapter()->quoteInto('id IN (?)', array_values($ids))
            );
            $res = true;
        }
        $this->_helper->json($res);
    }

    /**
     * get create form
     *
     * @abstract
     * @return Zend_Form
     */
    abstract protected function _getCreateForm();

    /**
     * get edit form
     *
     * @abstract
     * @return Zend_Form
     */
    abstract protected function _getEditForm();

    /**
     * get table
     *
     * @abstract
     * @return Core_Db_Table_Abstract
     */
    abstract protected function _getTable();

    /**
     * @abstract
     */
    protected function _prepareHeader()
    {
        // can be empty
    }

    /**
     * @abstract
     */
    abstract protected function _prepareGrid();

    /**
     * load grid
     *
     * @return void
     */
    protected function _loadGrid()
    {
        $grid = new Core_Grid();
        $grid->setAdapter($this->_getSource())
            ->setCurrentPageNumber($this->_getParam('page', 1))
            ->setItemCountPerPage($this->_limit);

        if ($this->_getParam('orderColumn')) {
            $grid->setOrder($this->_getParam('orderColumn'), $this->_getParam('orderDirection', 'asc'));
        } elseif (sizeof($this->_orders)) {
            foreach ($this->_orders as $column => $direction) {
                $grid->setOrder($column, $direction);
            }
        }

        if ($this->_getParam('filterColumn')) {
            $grid->setFilter($this->_getParam('filterColumn'), $this->_getParam('filterValue'));
        }

        $this->_grid = $grid;
    }

    /**
     * add radio column to grid
     *
     * @return Core_Controller_Action_Crud
     */
    public function _addCheckBoxColumn()
    {
        $this->_grid->setColumn(
            'check',
            array(
                'name'      => '<input type="checkbox" id="selectAllCheckbox"/>',
                'formatter' => array($this, 'checkBoxLinkFormatter'),
                'attribs'   => array('width' => '14px')
            )
        );
        return $this;
    }
    /**
     * add column for created date
     *
     * @return Core_Controller_Action_Crud
     */
    public function _addCreatedColumn()
    {
        $this->_grid->setColumn(
            'created',
            array(
                'name'  => 'Created',
                'type'  => Core_Grid::TYPE_DATA,
                'index' => 'created',
                'attribs' => array('width'=>'120px')
            )
        );
        return $this;
    }

    /**
     * add all table columns to grid
     *
     * @return Core_Controller_Action_Crud
     */
    public function _addAllTableColumns()
    {
        foreach ($this->_getTable()->info(Zend_Db_Table::COLS) as $col) {
            $this->_grid->setColumn(
                $col,
                array(
                    'name'  => ucfirst($col),
                    'type'  => Core_Grid::TYPE_DATA,
                    'index' => $col
                )
            );
        }
        return $this;
    }


    /**
     * add edit column to grid
     *
     * @return Core_Controller_Action_Crud
     */
    public function _addEditColumn()
    {
        $this->_grid->setColumn(
            'edit',
            array(
                'name'      => 'Edit',
                'formatter' => array($this, 'editLinkFormatter'),
                'attribs'   => array('width' => '60px')
            )
        );
        return $this;
    }

    /**
     * add delete column to grid
     *
     * @return Core_Controller_Action_Crud
     */
    public function _addDeleteColumn()
    {
        $this->_grid->setColumn(
            'delete',
            array(
                'name'      => 'Delete',
                'formatter' => array($this, 'deleteLinkFormatter'),
                'attribs'   => array('width' => '60px')
            )
        );
        return $this;
    }


    /**
     * edit link formatter
     *
     * @param $value
     * @param $row
     * @return string
     */
    public function editLinkFormatter($value, $row)
    {
        $link = '<a href="%s" class="btn span1">Edit</a>';
        $url = $this->getHelper('url')->url(
            array(
                'action' => 'edit',
                'id'     => $row['id']
            ),
            'default'
        );

        return sprintf($link, $url);
    }

    /**
     * delete link formatter
     *
     * @param $value
     * @param $row
     * @return string
     */
    public function deleteLinkFormatter($value, $row)
    {
        $link = '<a href="%s" class="btn btn-danger span1 delete">Delete</a>';
        $url = $this->getHelper('url')->url(
            array(
                'action' => 'delete',
                'id'     => $row['id']
            ),
            'default'
        );

        return sprintf($link, $url);
    }

    /**
     * delete link formatter
     *
     * @param $value
     * @param $row
     * @return string
     */
    public function checkBoxLinkFormatter($value, $row)
    {
        return '<input type="checkbox" name="id" value="' . $row['id'] . '"/>';
    }

    /**
     * @param $value
     * @param $row
     * @return string
     */
    public function trimFormatter($value, $row)
    {
        if (strlen($value) >= 200) {
            if (false !== ($breakpoint = strpos($value, ' ', 200))) {
                if ($breakpoint < strlen($value) - 1) {
                    $value = substr($value, 0, $breakpoint) . ' ...';
                }
            }
        }
        return $value;
    }

    /**
     * @param $value
     * @param $row
     * @return string
     */
    public function stripTagsFormatter($value, $row)
    {
        return strip_tags($value);
    }

    /**
     * add create button
     *
     * @return void
     */
    protected function _addCreateButton()
    {
        $link = '<a href="%s" class="btn btn-primary span1">Create</a>';
        $url = $this->getHelper('url')->url(array('action' => 'create'), 'default');
        $this->view->placeholder('grid_buttons')->create = sprintf($link, $url);
    }

    /**
     * add delete button
     *
     * @return void
     */
    protected function _addDeleteButton()
    {
        $link = '<a href="%s" class="btn btn-danger span1" id="delete-all-button">Delete All</a>';
        $url = $this->getHelper('url')->url(
            array(
                'action' => 'delete-all'
            ),
            'default'
        );
        $this->view->placeholder('grid_buttons')->deleteAll = sprintf($link, $url);
    }


    /**
     * add filter to stack
     *
     * @param $field
     * @param $label
     * @return \Core_Controller_Action_Crud
     */
    protected function _addFilter($field, $label)
    {
        $this->_filters[$field] = $label;
        return $this;
    }


    /**
     * get source
     *
     * @return Core_Grid_Adapter_AdapterInterface
     */
    protected function _getSource()
    {
        return new Core_Grid_Adapter_Select($this->_getTable()->select());
    }

    /**
     * set default script path
     *
     * @return Core_Controller_Action_Crud
     */
    protected function _setDefaultScriptPath()
    {
        $this->_viewRenderer->setViewScriptPathSpec('/:controller/:action.:suffix');
        return $this;
    }
}

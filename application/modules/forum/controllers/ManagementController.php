<?php
/**
 * UsersController for admin module
 *
 * @category   Application
 * @package    Users
 * @subpackage Controller
 *
 * @version  $Id: ManagementController.php 48 2010-02-12 13:23:39Z AntonShevchuk $
 */
class Forum_ManagementController extends Core_Controller_Action_Crud
{

    /**
     * Declare the source used to fetch the authors
     *
     * @return Core_Grid_Adapter_AdapterInterface
     */
    protected function _getSource()
    {
        return new Core_Grid_Adapter_Select(
            $this->_getTable()
                 ->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                 ->setIntegrityCheck(false)
                 ->joinLeft('users','users.id=forum_post.userId', array('login'))
                 ->joinLeft('categories','categories.id=forum_post.categoryId', array('category'=>'title'))
        );
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
        return new Forum_Form_Admin_Create();
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
        $form = new Forum_Form_Admin_Create();
        $form->addElement(new Zend_Form_Element_Hidden('id'));
        return $form;
    }

    /**
     * _getTable
     *
     * return manager for scaffolding
     *
     * @return  Core_Model_Abstract
     */
    protected function _getTable()
    {
        return new Forum_Model_Post_Table();
    }

    /**
     * _prepareHeader
     *
     * @return Forum_ManagementController
     */
    protected function _prepareHeader()
    {
        $this->_addCreateButton();
        $this->_addDeleteButton();
        $this->_addFilter('title', 'Title');
        $this->_addFilter('body', 'Text');
        $this->_addFilter('login', 'Author');
        return $this;
    }

    /**
     * change grid before render
     *
     * @return void
     */
    protected function _prepareGrid()
    {
        $this->_addCheckBoxColumn();
        $this->_grid->setColumn(
                        'title',
                        array(
                            'name'  => 'Title',
                            'type'  => Core_Grid::TYPE_DATA,
                            'index' => 'title',
                            'formatter' => array($this, 'titleFormatter'),
                        )
                    );
        $this->_grid->setColumn(
                                'login',
                                array(
                                    'name'  => 'Author',
                                    'type'  => Core_Grid::TYPE_DATA,
                                    'index' => 'login',
                                    'attribs' => array('width'=>'120px')
                                )
                            );
        $this->_grid->setColumn(
                                'category',
                                array(
                                    'name'  => 'Category',
                                    'type'  => Core_Grid::TYPE_DATA,
                                    'index' => 'category',
                                    'attribs' => array('width'=>'120px')
                                )
                            );
        $this->_grid->setColumn(
                                'comments',
                                array(
                                    'name'  => 'Comments',
                                    'type'  => Core_Grid::TYPE_DATA,
                                    'index' => 'comments',
                                    'attribs' => array('width'=>'50px'),
                                    'formatter' => array($this, 'badgeFormatter'),
                                )
                            );
//        $this->_grid->setColumn(
//                             'body',
//                             array(
//                                 'name'  => 'Text',
//                                 'type'  => Core_Grid::TYPE_DATA,
//                                 'index' => 'body',
//                                 'formatter' => array($this, array('trimFormatter'))
//                             )
//                         );
        $this->_addCreatedColumn();
        $this->_addEditColumn();
        $this->_addDeleteColumn();
    }

    /**
     * title link formatter
     *
     * @param $value
     * @param $row
     * @return string
     */
    public function titleFormatter($value, $row)
    {
        $link = '<a href="%s">%s</a>';
        $url = $this->getHelper('url')->url(
            // blog/post/:alias
            array(
                'module' => 'forum',
                'controller' => 'post',
                'id' => $row['id'],
            ),
            'forumpost'
        );

        return sprintf($link, $url, $value);
    }

    /**
     * comments count formatter
     *
     * @param $value
     * @param $row
     * @return string
     */
    public function badgeFormatter($value, $row)
    {
        $badge = '<span class="badge badge-info">%s</span>';
        return sprintf($badge, $value);
    }
}


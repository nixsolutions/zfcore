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
class Blog_ManagementController extends Core_Controller_Action_Crud
{

    /**
     * module statistic
     *
     * @return void
     */
    public function statsAction()
    {
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $this->view->totalPosts = $adapter->fetchOne('SELECT COUNT(*) FROM `blog_post`');
        $this->view->publicPosts = $adapter->fetchOne(
            'SELECT COUNT(*) FROM `blog_post` WHERE `status` = ?',
            array(Blog_Model_Post::STATUS_PUBLISHED)
        );
        // works with MySQL, Oracle, and SQL Server
        // @see http://justinsomnia.org/2004/06/how-to-count-unique-records-with-sql/
        $this->view->activeUsers = $adapter->fetchOne('SELECT COUNT(DISTINCT userId) FROM `blog_post`');
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
        return new Blog_Form_Admin_Create();
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
        $form = new Blog_Form_Admin_Create();
        $form->addElement(new Zend_Form_Element_Hidden('id'));
        return $form;
    }

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
                 ->joinLeft('users','users.id=blog_post.userId', array('login'))
                 ->joinLeft('categories','categories.id=blog_post.categoryId', array('category'=>'title'))
        );
    }

    /**
     * _getTable
     *
     * return manager for scaffolding
     *
     * @return  Blog_Model_Post_Table
     */
    protected function _getTable()
    {
        return new Blog_Model_Post_Table();
    }

    /**
     * custom grid filters
     *
     * @return void
     */
    protected function _prepareHeader()
    {
        $this->_addCreateButton();
        $this->_addDeleteButton();
        $this->_addFilter('title', 'Title');
        $this->_addFilter('login', 'Author');
    }

    /**
     * setup grid
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
                'module' => 'blog',
                'controller' => 'post',
                'alias' => $row['alias'],
            ),
            'blogpost'
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


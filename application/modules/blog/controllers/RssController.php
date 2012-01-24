<?php
/**
 * IndexController for Blog module
 *
 * @category   Application
 * @package    Blog
 * @subpackage Controller
 *
 * @version  $Id: PostController.php 164 2010-07-19 14:01:34Z dmitriy.britan $
 */
class Blog_RssController extends Core_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    /**
     * Main blog rss
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function indexAction()
    {
        $limit = 10;

        $url = $this->_helper->url;
        $serverUrl = $this->_request->getScheme()
                   . '://'
                   . $this->_request->getHttpHost();

        $feed = new Zend_Feed_Writer_Feed;
        $feed->setTitle('Blog Rss Feed');
        $feed->setLink($serverUrl . $url->url(array(), 'blog'));
        $feed->setFeedLink('http://www.example.com/atom', 'atom');
        $feed->addAuthor(
            array(
                'name'  => 'Blog Owner Name',
                'email' => 'support@example.com',
                'uri'   => $serverUrl,
            )
        );

        $posts = new Blog_Model_Post_Table();
        $select = $posts->getSelect();

        $feed->setDateModified(time());
        foreach ($posts->fetchAll($select->limit($limit)) as $i => $row) {
            if (0 == $i) {
                $feed->setDateModified(strtotime($row->updated));
            }
            $postUrl = $url->url(array('alias' => $row->alias), 'blogpost');

            $entry = $feed->createEntry();
            $entry->setTitle($row->title);
            $entry->setLink($serverUrl . $postUrl);
            $entry->addAuthor($row->login, null, null);

            $entry->setDateModified(strtotime($row->updated));
            $entry->setDateCreated(strtotime($row->published));
            $entry->setDescription($row->teaser);

            $feed->addEntry($entry);
        }

        echo $feed->export('atom');
    }

    /**
     * Category blog rss
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function categoryAction()
    {
        $limit = 10;

        if (!$alias = $this->_getParam('alias')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        $category = new Blog_Model_Category_Manager();
        if (!$row = $category->getByAlias($alias)) {
            throw new Zend_Controller_Action_Exception('Blog not found');
        }

        $url = $this->_helper->url;
        $serverUrl = $this->_request->getScheme()
                   . '://'
                   . $this->_request->getHttpHost();

        $title = $row->title . " Blog Rss Feed";
        $link = $url->url(array('alias' => $row->alias), 'blogcategory');

        $feed = new Zend_Feed_Writer_Feed;
        $feed->setTitle($title);
        $feed->setLink($serverUrl . $link);
        $feed->setFeedLink('http://www.example.com/atom', 'atom');
        $feed->addAuthor(
            array(
                'name'  => 'Blog Owner Name',
                'email' => null,
                'uri'   => $serverUrl,
            )
        );

        $posts = new Blog_Model_Post_Table();
        $select = $posts->getSelect($row);

        $feed->setDateModified(time());
        foreach ($posts->fetchAll($select->limit($limit)) as $i => $row) {
            if (0 == $i) {
                $feed->setDateModified(strtotime($row->updated));
            }
            $postUrl = $url->url(array('alias' => $row->alias), 'blogpost');

            $entry = $feed->createEntry();
            $entry->setTitle($row->title);
            $entry->setLink($serverUrl . $postUrl);
            $entry->addAuthor($row->login, null, null);

            $entry->setDateModified(strtotime($row->updated));
            $entry->setDateCreated(strtotime($row->published));
            $entry->setDescription($row->teaser);

            $feed->addEntry($entry);
        }

        echo $feed->export('atom');
    }

    /**
     * Author's blog rss
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function authorAction()
    {
        $limit = 10;

        if (!$login = $this->_getParam('login')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        $users = new Users_Model_Users_Table();
        if (!$user = $users->getByLogin($login)) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

        $url = $this->_helper->url;
        $serverUrl = $this->_request->getScheme()
                   . '://'
                   . $this->_request->getHttpHost();

        $title = ucfirst($user->login) . "'s Blog Rss Feed";
        $link = $url->url(array('login' => $user->login), 'blogauthor');

        $feed = new Zend_Feed_Writer_Feed;
        $feed->setTitle($title);
        $feed->setLink($serverUrl . $link);
        $feed->setFeedLink('http://www.example.com/atom', 'atom');
        $feed->addAuthor(
            array(
                'name'  => 'Blog Owner Name',
                'email' => $user->email,
                'uri'   => $serverUrl,
            )
        );

        $posts = new Blog_Model_Post_Table();
        $select = $posts->getSelect(null, $user->id);

        $feed->setDateModified(time());
        foreach ($posts->fetchAll($select->limit($limit)) as $i => $row) {
            if (0 == $i) {
                $feed->setDateModified(strtotime($row->updated));
            }
            $postUrl = $url->url(array('alias' => $row->alias), 'blogpost');

            $entry = $feed->createEntry();
            $entry->setTitle($row->title);
            $entry->setLink($serverUrl . $postUrl);
            $entry->addAuthor($row->login, null, null);

            $entry->setDateModified(strtotime($row->updated));
            $entry->setDateCreated(strtotime($row->published));
            $entry->setDescription($row->teaser);

            $feed->addEntry($entry);
        }

        echo $feed->export('atom');
    }
}
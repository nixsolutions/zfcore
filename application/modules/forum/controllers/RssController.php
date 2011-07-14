<?php
/**
 * RssController for Forum module
 *
 * @category   Application
 * @package    Forum
 * @subpackage Controller
 *
 * @version  $Id$
 */
class Forum_RssController extends Core_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    /**
     * Main forum rss
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
        $feed->setTitle('Forum Rss Feed');
        $feed->setLink($serverUrl . $url->url(array(), 'forum'));
        $feed->setFeedLink('http://www.example.com/atom', 'atom');
        $feed->addAuthor(array(
            'name'  => 'Forum Owner Name',
            'email' => 'support@example.com',
            'uri'   => $serverUrl,
        ));

        $posts = new Forum_Model_Post_Table();
        $select = $posts->getPostsSelect();

        $feed->setDateModified(time());
        foreach ($posts->fetchAll($select->limit($limit)) as $i => $row) {
            if (0 == $i) {
                $feed->setDateModified(strtotime($row->updated));
            }
            $postUrl = $url->url(array('id' => $row->id), 'forumpost');

            $entry = $feed->createEntry();
            $entry->setTitle($row->title);
            $entry->setLink($serverUrl . $postUrl);
            $entry->addAuthor($row->author, null, null);

            $entry->setDateModified(strtotime($row->updated));
            $entry->setDateCreated(strtotime($row->created));
            $entry->setDescription($row->getTeaser());

            $feed->addEntry($entry);
        }

        echo $feed->export('atom');
    }

    /**
     * Category forum rss
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function categoryAction()
    {
        $limit = 10;

        if (!$alias = $this->_getParam('alias')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        $category = new Forum_Model_Category_Manager();
        if (!$row = $category->getByAlias($alias)) {
            throw new Zend_Controller_Action_Exception('Forum not found');
        }

        $url = $this->_helper->url;
        $serverUrl = $this->_request->getScheme()
                   . '://'
                   . $this->_request->getHttpHost();

        $title = $row->title . " Forum Rss Feed";
        $link = $url->url(array('alias' => $row->alias), 'forumcategory');

        $feed = new Zend_Feed_Writer_Feed;
        $feed->setTitle($title);
        $feed->setLink($serverUrl . $link);
        $feed->setFeedLink('http://www.example.com/atom', 'atom');
        $feed->addAuthor(
            array(
                'name'  => 'Forum Owner Name',
                'email' => null,
                'uri'   => $serverUrl,
            )
        );

        $posts = new Forum_Model_Post_Table();
        $select = $posts->getPostsSelect($row->id);

        $feed->setDateModified(time());
        foreach ($posts->fetchAll($select->limit($limit)) as $i => $row) {
            if (0 == $i) {
                $feed->setDateModified(strtotime($row->updated));
            }
            $postUrl = $url->url(array('id' => $row->id), 'forumpost');

            $entry = $feed->createEntry();
            $entry->setTitle($row->title);
            $entry->setLink($serverUrl . $postUrl);
            $entry->addAuthor($row->author, null, null);

            $entry->setDateModified(strtotime($row->updated));
            $entry->setDateCreated(strtotime($row->created));
            $entry->setDescription($row->getTeaser());

            $feed->addEntry($entry);
        }

        echo $feed->export('atom');
    }

    /**
     * Post forum rss
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function postAction()
    {
        $limit = 10;

        if (!$postId = $this->_getParam('id')) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        $posts = new Forum_Model_Post_Table();
        if (!$row = $posts->getByid($postId)) {
            throw new Zend_Controller_Action_Exception('Post not found');
        }

        $url = $this->_helper->url;
        $serverUrl = $this->_request->getScheme()
                   . '://'
                   . $this->_request->getHttpHost();

        $title = $row->title . " Post Rss Feed";
        $link = $url->url(array('id' => $row->id), 'forumpost');

        $feed = new Zend_Feed_Writer_Feed;
        $feed->setTitle($title);
        $feed->setLink($serverUrl . $link);
        $feed->setDescription($row->getTeaser());
        $feed->setFeedLink('http://www.example.com/atom', 'atom');
        $feed->addAuthor(
            array(
                'name'  => 'Forum Owner Name',
                'email' => null,
                'uri'   => $serverUrl,
            )
        );

        $comments = new Forum_Model_Comment_Table();
        $select = $comments->getCommentsSelect($row->id);
        $select->order('created DESC');
        $select->limit($limit);

        $feed->setDateModified(time());
        foreach ($comments->fetchAll($select) as $i => $row) {
            if (0 == $i) {
                $feed->setDateModified(strtotime($row->updated));
            }
            $postUrl = $url->url(array('id' => $row->id), 'forumpost');

            $entry = $feed->createEntry();
            $entry->setTitle($row->title);
            $entry->setLink($serverUrl . $postUrl);
            $entry->addAuthor($row->login, null, null);

            $entry->setDateModified(strtotime($row->updated));
            $entry->setDateCreated(strtotime($row->created));
            $entry->setDescription($row->body);

            $feed->addEntry($entry);
        }

        echo $feed->export('atom');
    }
}
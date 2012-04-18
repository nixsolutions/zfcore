<?php
/**
 * RssController for Forum module
 *
 * @category   Application
 * @package    Forum
 * @subpackage Controller
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
        $feed->setTitle('Forum RSS Feed');
        $feed->setLink($serverUrl . $url->url(array('module'=>'forum')));
        $feed->setFeedLink($serverUrl . $url->url(array('module'=>'forum')), 'atom');
        $feed->addAuthor(
            array(
                'name'  => 'Forum Owner Name',
                'email' => 'support@example.com',
                'uri'   => $serverUrl,
            )
        );

        $posts = new Forum_Model_Post_Table();
        $select = $posts->getPostsSelect();
        $select->limit($limit);

        $feed->setDateModified(time());
        foreach ($posts->fetchAll($select) as $i => $row) {
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
            throw new Zend_Controller_Action_Exception('Forum not found');
        }
        $category = new Forum_Model_Category_Manager();
        if (!$row = $category->getByAlias($alias)) {
            throw new Zend_Controller_Action_Exception('Forum not found');
        }

        $url = $this->_helper->url;
        $serverUrl = $this->_request->getScheme()
                   . '://'
                   . $this->_request->getHttpHost();

        $title = $row->title . " // Topics RSS Feed";
        $link = $url->url(array('alias' => $row->alias), 'forumcategory');

        $feed = new Zend_Feed_Writer_Feed;
        $feed->setTitle($title);
        $feed->setLink($serverUrl . $link);
        $feed->setFeedLink($serverUrl . $url->url(array('module'=>'forum')), 'atom');
        $feed->addAuthor(
            array(
                'name'  => 'Forum Owner Name',
                'email' => null,
                'uri'   => $serverUrl,
            )
        );

        $posts = new Forum_Model_Post_Table();
        $select = $posts->getPostsSelect($row->id);
        $select->limit($limit);

        $feed->setDateModified(time());
        foreach ($posts->fetchAll($select) as $i => $row) {
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
     * @todo should return comments of topic
     * @throws Zend_Controller_Action_Exception
     */
    public function postAction()
    {
        if (!$postId = $this->_getParam('id')) {
            throw new Zend_Controller_Action_Exception('Topic not found');
        }
        $posts = new Forum_Model_Post_Table();
        if (!$row = $posts->getByid($postId)) {
            throw new Zend_Controller_Action_Exception('Topic not found');
        }

        $url = $this->_helper->url;
        $serverUrl = $this->_request->getScheme()
                   . '://'
                   . $this->_request->getHttpHost();

        $title = $row->title . " // Comments RSS Feed";
        $link = $url->url(array('id' => $row->id), 'forumpost');

        $feed = new Zend_Feed_Writer_Feed;
        $feed->setTitle($title);
        $feed->setLink($serverUrl . $link);
        $feed->setDescription($row->getTeaser());
        $feed->setFeedLink($serverUrl . $url->url(array('module'=>'forum')), 'atom');
        $feed->addAuthor(
            array(
                'name'  => 'Forum Owner Name',
                'email' => null,
                'uri'   => $serverUrl,
            )
        );

        $feed->setDateModified(time());

        echo $feed->export('atom');
    }
}
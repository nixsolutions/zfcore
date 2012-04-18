<?php
/**
 * Comments_View_Helper_GetComments
 *
 * @see http://framework.zend.com/manual/en/performance.view.html#performance.view.action.model
 *
 * @version $Id$
 */
class Comments_View_Helper_GetComments extends Zend_View_Helper_Abstract
{
    protected $_key = 0;

    protected $_template;

    /**
     * Load the comments by the unique alias.
     *
     * Options:
     *      - template
     *      - key
     *
     * @param string $aliasKey
     * @param array $options
     * @return string
     * @throws Zend_Controller_Action_Exception
     */
    public function getComments($aliasKey, $options = array())
    {
        $user = Zend_Auth::getInstance()->getIdentity();

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $aliasManager = new Comments_Model_CommentAlias_Manager();
        $manager = new Comments_Model_Comment_Manager();

        $alias = $aliasManager->getByAlias($aliasKey);
        $page = $request->getParam('page');
        $userId = ($user) ? $user->id : 0;

        $this->_checkOptions($options);

        // throws Exception when aliasKey is missed or wrong
        if (!$aliasKey || !$alias) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }

        // throw Exception when key required and missed
        if (!$this->_key && $alias->isKeyRequired()) {
            throw new Zend_Controller_Action_Exception('Missed key parameter');
        }

        $paginator = Zend_Paginator::factory($manager->getSelect($alias, $userId, $this->_key));

        // set paginator options
        if ($alias->isPaginatorEnabled()) {
            $paginator->setItemCountPerPage($alias->countPerPage);
            $paginator->setCurrentPageNumber($page);
        } else {
            // Is there a way to disable pagination?
            $paginator->setItemCountPerPage(9999);
        }

        // init form
        $form = new Comments_Model_Comment_Form_Create();
        $form->setAlias($aliasKey);
        $form->setKey($this->_key);
        $form->setReturnUrl($this->view->url());

        if (!$alias->isTitleDisplayed()) {
            $form->removeTitleElement();
        }

        return $this->view->partial(
            'list.phtml',
            'comments',
            array(
                'paginator' => $paginator,
                'form' => $form,
                'user' => $user,
                'template' => $this->_template
            )
        );
    }

    /**
     * Check the stack of options
     *
     * @param array $options
     * @return void
     */
    private function _checkOptions($options = array())
    {
        if (isset($options['template'])) {
            $this->_template = $options['template'];
        }

        if (isset($options['key'])) {
            $this->_key = $options['key'];
        }
    }
}
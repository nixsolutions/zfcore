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
    protected $key = 0;
    
    protected $template;
    
    /**
     * Load the comments by the unique alias.
     * 
     * Options:
     *      - enablePaginator
     *      - page
     * 
     * @param string $aliasKey
     * @param array $options
     * @param integer | mixed $page
     * @return type
     * @throws Zend_Controller_Action_Exception 
     */
    public function getComments($aliasKey, $options = array())
    {
        $user = $this->view->user();
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        
        $aliasManager = new Comments_Model_CommentAlias_Manager();
        $manager = new Comments_Model_Comment_Manager();
        
        $alias = $aliasManager->getByAlias($aliasKey);
        $page = $request->getParam('page');
        $userId = ($user) ? $user['id'] : 0;
        
        $this->_checkOptions($options);
        
        if (!$aliasKey) {
            throw new Zend_Controller_Action_Exception('Page not found');
        }
        
        if (!$alias) {
            throw new Zend_Controller_Action_Exception('Comment alias not found');
        }
        
        if (!$this->key && $alias->isKeyRequired()) {
            throw new Zend_Controller_Action_Exception('Missed key parameter');
        }
        
        $paginator = Zend_Paginator::factory($manager->getSelect($alias, $userId, $this->key));

        if ($alias->isPaginatorEnabled()) {
            $paginator->setItemCountPerPage($alias->countPerPage);
            $paginator->setCurrentPageNumber($page);
        } else {
            // Is there a way to disable pagination?
            $paginator->setItemCountPerPage(9999);
        }

        $form = new Comments_Model_Comment_Form_Create();
        $form->setAlias($aliasKey);
        $form->setKey($this->key);
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
                'template' => $this->template
            )
        );
    }
    
    private function _checkOptions($options = array())
    {
        if (isset($options['template'])) {
            $this->template = $options['template'];
        }
        
        if (isset($options['key'])) {
            $this->key = $options['key'];
        }
    }
}
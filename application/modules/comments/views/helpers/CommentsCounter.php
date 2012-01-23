<?php
/**
 * Comments_View_Helper_CommentsCounter
 *
 * @todo Opened for discussion
 * 
 * @see http://framework.zend.com/manual/en/performance.view.html#performance.view.action.model
 * @version $Id$
 */
class Comments_View_Helper_CommentsCounter extends Zend_View_Helper_Abstract
{

    /**
     * Fetch the amount of comments for the items related to the $alias with keys
     * 
     * @param mixed $key
     * @param string $alias
     * @param array $items
     * @param string $fieldName
     * @return array
     * @throws Zend_Controller_Action_Exception 
     */
    public function commentsCounter($key, $alias, $items, $fieldName = 'id')
    {
        $aliasManager = new Comments_Model_CommentAlias_Manager();
        
        $aliasRow = $aliasManager->getByAlias($alias);
        
        // check if alias registered
        if (!Zend_Registry::isRegistered($alias)) {
            $userId = ($this->view->user()) ? $this->view->user()->id : 0;
            $commentsManager = new Comments_Model_Comment_Manager();
            
            $values = array();
            
            // collect items
            foreach ($items as $item) {
                $value = (int) $item[$fieldName];
                
                if ($value > 0) {
                    array_push($values, $value);
                }
            }
            
            // fetch gropped comments amount
            $commentsAmount = $commentsManager->getGrouppedCommentsAmount(
                $aliasRow, 
                $fieldName,
                $values,
                $userId
            );
            
            Zend_Registry::set(
                $alias, 
                $commentsAmount
            );
        }
        
        // fetch the data from registry
        $commentsAmount = Zend_Registry::get($alias);
        
        if (isset($commentsAmount[$key])) {
            return $commentsAmount[$key];
        } else {
            return 0;
        }
    }
}
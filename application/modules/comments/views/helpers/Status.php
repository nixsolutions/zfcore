<?php
/**
 * @author Pavel Machekhin
 */
class Comments_View_Helper_Status extends Zend_View_Helper_Abstract
{
    /**
     * Display the comment status.
     * 
     * @param string $aliasKey
     * @param array $options
     * @param integer | mixed $page
     * @return type
     * @throws Zend_Controller_Action_Exception 
     */
    
    
    public function status(Comments_Model_Comment $comment)
    {
        $text = '';
        
        switch ($comment->status) {
            case Comments_Model_Comment::STATUS_REVIEW:
                $text .= '<p class="warning">Comment is currently under review</p>';
                break;
            case Comments_Model_Comment::STATUS_DELETED:
                $text .= '<p class="warning">Comment is deleted by administrator</p>';
                break;
            default :
                break;
        }
        
        return $text;
    }
}
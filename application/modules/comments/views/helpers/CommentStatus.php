<?php
/**
 * Display the warning text to user when comment is not active.
 * 
 * @author Pavel Machekhin
 */
class Comments_View_Helper_CommentStatus extends Zend_View_Helper_Abstract
{
    /**
     * Display the comment status.
     * 
     * @param Comments_Model_Comment $comment
     * @return string 
     */
    public function commentStatus(Comments_Model_Comment $comment)
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
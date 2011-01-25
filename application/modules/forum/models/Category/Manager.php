<?php
/**
 * Category DBTable
 *
 * @category Application
 * @package Model
 * @subpackage Category
 * 
 * @author Ivan Nosov aka rewolf <i.k.nosov@gmail.com>
 *
 * @version  $Id: Manager.php 163 2010-07-12 16:30:02Z AntonShevchuk $
 */
class Forum_Model_Category_Manager extends Core_Model_Manager
{
    /** template for tree */
    protected $_template = array(
        'start' => "<ul>",
        'end' => "</ul>",
        'catStart' => "<li>",
        'catEnd' => "</li>"
    );

    /**
     * get categories
     *
     * @return array
     */
    public function getCategories($idParentCategory = 0)
    {
        $select = $this->getDbTable()->select()
                ->from(
                    array('c' => 'bf_category'),
                    array(
                        '*',
                        'count_posts' => new Zend_Db_Expr('COUNT(DISTINCT(p.id))'),
                        'count_comments' => new Zend_Db_Expr('COUNT(com.id)'),
                    )
                )
                ->joinLeft(
                    array('p' => 'bf_post'),
                    'p.ctg_id = c.id', array()
                )
                ->joinLeft(
                    array('com' => 'bf_comment'),
                    'p.id = com.post_id', array()
                )
                ->where('ctg_parent_id = ?', $idParentCategory)
                ->group('c.id');
        return $this->getDbTable()->fetchAll($select)->toArray();
    }
    
    /**
     * get tree of categories
     *
     * @return string
     */
    public function getTreeCategories() 
    {
        $result = $this->getDbTable()->fetchAll($this->getDbTable()->select())->toArray();
        $result = $this->prepareCategories($result);
        return $this->createTree($result);
    }
    
    /**
     * get all categories
     */
    public function getAllCategories()
    {
        $result = $this->getDbTable()->fetchAll($this->getDbTable()->select())->toArray();
        $categories = array();
        foreach ($result as $cat) {
            $categories[$cat['id']] = $cat['ctg_title'];
        }
        return $categories;
    }

    /**
     * prepare categories for future manipulation
     *
     * @param array $arr
     * @return array
     */
    private function prepareCategories($arr)
    {
        $res = array();
        foreach ($arr as $v) {
            $res[$v['ctg_parent_id']][$v['id']] = $v;
        }
        return $res;
    }

    /**
     * create tree of categories
     *
     * @param array $arr
     * @param integer $parrent
     * @return string
     */
    private function createTree($arr, $parrent = 0) 
    {
        if (count($arr[$parrent]) == 0) return '';
        $result = $this->_template['start'];
        foreach ($arr[$parrent] as $cat) {
            $result .= $this->_template['catStart']
                     . '<a href="' . Zend_View_Helper_Url::url(array('id' => $cat['id'])) . '">'
                     . $cat['ctg_title'] . '</a>'
                     . $this->_template['catEnd'];
            $subArr = $arr;
            unset($subArr[$parrent]);
            $result .= $this->createTree($subArr, $cat['id']);
        }
        $result .= $this->_template['end'];
        return $result;
    }
    
    /**
     * set template for tree
     *
     * @param array $arr
     * @return Model_Category
     */
    public function setTemplate(array $arr)
    {
        $this->_template = $arr;
        return $this;
    }
    
    /**
     * get template for tree
     *
     * @return array
     */
    private function getTemplate()
    {
        return $this->_template;
    }

}
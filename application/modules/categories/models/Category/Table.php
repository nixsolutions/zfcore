<?php
/**
 * Categories_Model_Category_Table
 *
 *
 * @example $categories = new Categories_Model_Category_Table();
 *          $category = $categories->getById(1);
 *          $category->loadTree(); //load all child categories
 *
 *         $result = array();
 *         foreach ($category->getChildren() as $row) {
 *             $result[$row->id] = $row->toArray();
 *
 *             foreach ($row->getChildren() as $subrow) {
 *                 $result[$row->id]['children'][$subrow->id] = $subrow->toArray();
 *
 *                 foreach ($subrow->getChildren() as $subsubrow) {
 *
 *                     $result[$row->id]['children'][$subrow->id]['children'][$subsubrow->id] = $subsubrow->toArray();
 *                 }
 *             }
 *         }
 *
 *
 * @version $Id$
 */
class Categories_Model_Category_Table extends Core_Categories_Table
{
    /**
     * @var string
     */
    protected $_name = 'categories';

    /**
     * @var string
     */
    protected $_rowClass = 'Categories_Model_Category';
}
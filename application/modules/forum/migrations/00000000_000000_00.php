<?php

class Forum_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // category table
        $this->insert('categories', array(
            'id'              =>  '1',
            'title'       =>  'forum',
            'description' =>  'forum',
            'alias'   =>  'forum',
            'path'   =>  'forum',
        ));

        // post table
        $this->createTable('bf_post');

        $this->createColumn('bf_post',
                            'post_title',
                            Core_Migration_Abstract::TYPE_TEXT,
                            null, null, true);

        $this->createColumn('bf_post',
                            'post_text',
                            Core_Migration_Abstract::TYPE_TEXT,
                            null, null, true);

        $this->createColumn('bf_post',
                            'ctg_id',
                            Core_Migration_Abstract::TYPE_INT,
                            null, null, true);

        $this->createColumn('bf_post',
                            'user_id',
                            Core_Migration_Abstract::TYPE_INT,
                            null, null, true);

        $this->createColumn('bf_post',
                            'post_status',
                            Core_Migration_Abstract::TYPE_ENUM,
                            array('active','closed','deleted'), null, true);

        $this->createColumn('bf_post',
                            'post_flag',
                            Core_Migration_Abstract::TYPE_INT,
                            null, '0', true);

        $this->createColumn('bf_post',
                            'post_view_count',
                            Core_Migration_Abstract::TYPE_INT,
                            null, '0', true);

        $this->createColumn('bf_post',
                            'post_reply_count',
                            Core_Migration_Abstract::TYPE_INT,
                            null, '0', true);

        $this->createColumn('bf_post',
                            'created',
                            Core_Migration_Abstract::TYPE_TIMESTAMP,
                            null, 'CURRENT_TIMESTAMP', true);

        $this->createColumn('bf_post',
                            'updated',
                            Core_Migration_Abstract::TYPE_TIMESTAMP,
                            null, null);

        $this->insert('bf_post', array(
            'post_title'    =>  'title',
            'post_text'   =>  'text',
            'ctg_id'=> '1',
            'user_id'=> '207',
            'post_status'=> 'active'
        ));

        // comment table
        $this->createTable('bf_comment');

        $this->createColumn('bf_comment',
                            'cmt_text',
                            Core_Migration_Abstract::TYPE_TEXT,
                            null, null, true);

        $this->createColumn('bf_comment',
                            'post_id',
                            Core_Migration_Abstract::TYPE_INT,
                            null, null, true);

        $this->createColumn('bf_comment',
                            'user_id',
                            Core_Migration_Abstract::TYPE_INT,
                            null, null, true);

        $this->createColumn('bf_comment',
                            'created',
                            Core_Migration_Abstract::TYPE_TIMESTAMP,
                            null, null, true);

        $this->createColumn('bf_comment',
                            'updated',
                            Core_Migration_Abstract::TYPE_TIMESTAMP,
                            null, null, true);
    }

    public function down()
    {
        $this->dropTable('bf_post');
        $this->dropTable('bf_comment');
    }
}
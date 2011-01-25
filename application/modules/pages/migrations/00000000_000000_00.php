<?php

class Pages_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // static pages table
        $this->createTable('pages');
        
        $this->createColumn('pages', 
                            'pid', 
                            Core_Migration_Abstract::TYPE_BIGINT, 
                            null, null, true, true);
          
        $this->createColumn('pages', 
                            'title', 
                            Core_Migration_Abstract::TYPE_TEXT, 
                            null, null, true);
                            
        $this->createColumn('pages', 
                            'alias', 
                            Core_Migration_Abstract::TYPE_VARCHAR, 
                            255, null, true);
                            
        $this->createColumn('pages', 
                            'content', 
                            Core_Migration_Abstract::TYPE_LONGTEXT);
                            
        $this->createColumn('pages', 
                            'keywords', 
                            Core_Migration_Abstract::TYPE_TEXT);
                            
        $this->createColumn('pages', 
                            'description', 
                            Core_Migration_Abstract::TYPE_TEXT);
                            
        $this->createColumn('pages', 
                            'created', 
                            Core_Migration_Abstract::TYPE_TIMESTAMP,
                            null,
                            'CURRENT_TIMESTAMP', true);
                            
        $this->createColumn('pages', 
                            'updated', 
                            Core_Migration_Abstract::TYPE_TIMESTAMP,
                            null,
                            '2000-01-01 00:00', true);
                            
        $this->createColumn('pages', 
                            'user_id', 
                            Core_Migration_Abstract::TYPE_BIGINT,
                            null, '1', true);
                            
        $this->createUniqueIndexes('pages', array('pid', 'alias'), 'unique');
    }

    public function down()
    {
        $this->dropTable('pages');
    }
}


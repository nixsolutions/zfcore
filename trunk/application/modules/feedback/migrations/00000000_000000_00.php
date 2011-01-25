<?php

class Feedback_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // feedback table
        $this->createTable('feedback');
        
        $this->createColumn('feedback',
                            'sender', 
                            Core_Migration_Abstract::TYPE_TEXT, 
                            null, null, true);
                            
        $this->createColumn('feedback',
                            'subject', 
                            Core_Migration_Abstract::TYPE_TEXT, 
                            null, null, true);
                            
        $this->createColumn('feedback',
                            'message', 
                            Core_Migration_Abstract::TYPE_TEXT, 
                            null, null, true);
                            
        $this->createColumn('feedback',
                            'email', 
                            Core_Migration_Abstract::TYPE_VARCHAR, 
                            80, null, true);
          
        $this->createColumn('feedback',
                            'status', 
                            Core_Migration_Abstract::TYPE_ENUM,
                            array('New', 'Read', 'Reply', 'Edit', 'Delete'), 'New', true);
                            
        $this->createColumn('feedback',
                            'created', 
                            Core_Migration_Abstract::TYPE_TIMESTAMP, 
                            null, 'CURRENT_TIMESTAMP');
       
        $this->createColumn('feedback',
                            'updated', 
                            Core_Migration_Abstract::TYPE_TIMESTAMP, 
                            null, '2000-01-01 00:00');
                            
        $this->createColumn('feedback',
                            'data', 
                            Core_Migration_Abstract::TYPE_TEXT,
                            null, null);
    }

    public function down()
    {
        $this->dropTable('feedback');
    }
}


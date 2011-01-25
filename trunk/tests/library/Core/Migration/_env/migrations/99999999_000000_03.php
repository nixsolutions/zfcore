<?php

class Migration_99999999_000000_03 extends Core_Migration_Abstract
{
    public function up()
    {
        // options table
        $this->createTable('items_03');
        
        $this->createColumn('items_03', 
                            'name', 
                            Core_Migration_Abstract::TYPE_VARCHAR, 
                            255, null, true, true);
                            
        $this->createColumn('items_03', 
                            'desc', 
                            Core_Migration_Abstract::TYPE_LONGTEXT, 
                            null, null, true);
        
        // insert data about revision number ZERO
       
        $this->insert('items_03', array(
            'name'  => 'simpleName',  
            'desc' => 'Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum',
        ));
    }

    public function down()
    {
        $this->dropTable('items_03');
    }
}


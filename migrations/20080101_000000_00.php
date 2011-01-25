<?php

class Migration_20080101_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // options table
        $this->createTable('options');
        
        $this->createColumn('options', 
                            'name', 
                            Core_Migration_Abstract::TYPE_VARCHAR, 
                            255, null, true, true);
                            
        $this->createColumn('options', 
                            'value', 
                            Core_Migration_Abstract::TYPE_LONGTEXT, 
                            null, null, true);
                            
        $this->createColumn('options', 
                            'type', 
                            Core_Migration_Abstract::TYPE_ENUM, 
                            array('int','float','string','array','object'), 'string', true);
                            
        $this->createColumn('options', 
                            'namespace', 
                            Core_Migration_Abstract::TYPE_VARCHAR, 
                            64, 'default', true, true);
        
        // insert data about revision number ZERO
       
        $this->insert('options', array(
            'name'    =>  'migration',  
            'value'   =>  '00000000_000000_00',
            'namespace'=> 'default'
        ));
    }

    public function down()
    {
        $this->dropTable('options');
    }
}


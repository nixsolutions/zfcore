<?php

class Simplemodule_Migration_99999999_000000_02 extends Core_Migration_Abstract
{
    public function up()
    {
        // options table
        $this->createTable('items_s2');
        
        $this->createColumn(
            'items_s2',
            'name',
            Core_Migration_Abstract::TYPE_VARCHAR,
            255, null, true, true
        );
                            
        $this->createColumn(
            'items_s2',
            'value',
            Core_Migration_Abstract::TYPE_INT,
            null, null, true
        );
        
        // insert data about revision number ZERO
       
        $this->insert(
            'items_s2',
            array(
                'name'  => 'simpleName',
                'value' => 765,
            )
        );
    }

    public function down()
    {
        $this->dropTable('items_s2');
    }
}


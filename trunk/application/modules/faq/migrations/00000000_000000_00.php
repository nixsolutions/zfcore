<?php

class Faq_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // faq table
        $this->createTable('faq');
        
        $this->createColumn('faq',
                            'question', 
                            Core_Migration_Abstract::TYPE_TEXT, 
                            null, null, true);
                            
        $this->createColumn('faq',
                            'answer', 
                            Core_Migration_Abstract::TYPE_TEXT, 
                            null, null, true);
    }

    public function down()
    {
        $this->dropTable('faq');
    }
}
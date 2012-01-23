<?php

class Faq_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        $this->query(
            "
            CREATE TABLE `faq` (
              `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
              `question` TEXT NOT NULL,
              `answer` TEXT NOT NULL,
              PRIMARY KEY  (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            "
        );
    }

    public function down()
    {
        $this->dropTable('faq');
    }
}
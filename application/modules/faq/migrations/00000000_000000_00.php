<?php

class Faq_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        $this->query("
            CREATE TABLE `faq` (
              `id` int(10) NOT NULL AUTO_INCREMENT,
              `question` text NOT NULL,
              `answer` text NOT NULL,
              `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
              `updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8
            "
        );
    }

    public function down()
    {
        $this->dropTable('faq');
    }
}
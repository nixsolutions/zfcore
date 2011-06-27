<?php

class Faq_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // faq table
        $this->query("CREATE TABLE `categories` (
              `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
              `alias` VARCHAR(255) DEFAULT NULL,
              `title` TEXT NOT NULL,
              `description` TEXT NOT NULL,
              `parentId` INT(10) UNSIGNED NOT NULL DEFAULT '0',
              `level` INT(11) NOT NULL DEFAULT '0',
              `path` TEXT,
              PRIMARY KEY  (`id`)
            ) ENGINE=INNODB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ");
    }

    public function down()
    {
        $this->dropTable('categories');
    }
}
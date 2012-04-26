<?php

class Categories_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // faq table
        $this->query("
            CREATE TABLE `categories` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `alias` varchar(255) NOT NULL,
              `title` varchar(512) NOT NULL,
              `description` text NOT NULL,
              `parentId` int(10) unsigned NOT NULL DEFAULT '0',
              `level` int(11) NOT NULL DEFAULT '0',
              `path` text NOT NULL,
              `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              PRIMARY KEY (`id`),
              UNIQUE KEY `categories_path` (`path`(128)),
              KEY `categories_parent` (`parentId`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");
    }

    public function down()
    {
        $this->dropTable('categories');
    }
}
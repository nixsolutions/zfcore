<?php

class Pages_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // static pages table
        $this->query(
            "CREATE TABLE `pages` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `pid` int(10) unsigned NOT NULL COMMENT 'Parent Page',
              `title` text NOT NULL,
              `alias` varchar(255) NOT NULL COMMENT 'Key for permalinks',
              `content` longtext,
              `keywords` text COMMENT 'Meta Keywords',
              `description` text COMMENT 'Meta Description',
              `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              `userId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Author, can be zero',
              PRIMARY KEY (`id`,`pid`),
              UNIQUE KEY `unique` (`pid`,`alias`),
              KEY `FK_pages_to_users` (`userId`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
        );
    }

    public function down()
    {
        $this->dropTable('pages');
    }
}


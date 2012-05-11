<?php

class Comments_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // create comments table
        $this->query(
            "
            CREATE TABLE `comment_aliases` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `alias` varchar(255) NOT NULL,
              `options` text,
              `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              `countPerPage` smallint(5) NOT NULL DEFAULT '10',
              `relatedTable` varchar(64) DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `comment_aliases_unique` (`alias`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            CREATE TABLE `comments` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `aliasId` int(10) unsigned NOT NULL,
              `key` bigint(20) unsigned NOT NULL,
              `userId` bigint(20) unsigned NOT NULL,
              `parentId` bigint(20) unsigned DEFAULT NULL,
              `title` varchar(512) NOT NULL,
              `body` text,
              `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              `status` enum('active','banned','deleted','review') NOT NULL DEFAULT 'active',
              PRIMARY KEY (`id`),
              KEY `comments_target` (`aliasId`,`key`),
              KEY `FK_comments_to_users` (`userId`),
              CONSTRAINT `FK_comments_to_comment_aliases` FOREIGN KEY (`aliasId`) REFERENCES `comment_aliases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `FK_comments_to_users` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            "
        );
    }

    public function down()
    {
        // degrade
        $this->dropTable('comment_aliases');
        $this->dropTable('comments');
    }


}


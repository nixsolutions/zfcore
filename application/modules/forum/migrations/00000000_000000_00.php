<?php

class Forum_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // dependencies
        $this->getMigrationManager()->up('categories');
        $this->getMigrationManager()->up('comments');

        // post table
        $this->query("
            CREATE TABLE `forum_post` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `title` text NOT NULL,
              `body` text NOT NULL,
              `categoryId` int(10) unsigned NOT NULL,
              `userId` bigint(20) unsigned NOT NULL,
              `status` enum('active','closed','deleted') NOT NULL DEFAULT 'active',
              `views` int(10) unsigned NOT NULL DEFAULT '0',
              `comments` int(10) unsigned NOT NULL DEFAULT '0',
              `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              PRIMARY KEY (`id`),
              KEY `FK_forum_post_to_categories` (`categoryId`),
              KEY `FK_forum_post_to_users` (`userId`),
              CONSTRAINT `FK_forum_post_to_users` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `FK_forum_post_to_categories` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            "
        );

        // category table
        $this->insert('categories', array(
            'title'       =>  'Forum',
            'description' =>  '<strong>Forum module</strong>',
            'alias'       =>  'forum',
            'path'        =>  'forum',
        ));



        // comment_aliases table
        $this->insert('comment_aliases', array(
                    'alias'        => 'forum',
                    'options'      => '["keyRequired","titleDisplayed","paginatorEnabled"]',
                    'countPerPage' => 10,
                    'relatedTable' => 'forum_post',
        ));
    }

    public function down()
    {
        $this->query('DELETE FROM `categories` WHERE `alias` = ?', array('forum'));
        $this->query('DELETE FROM `comment_aliases` WHERE `alias` = ?', array('forum'));

        $this->dropTable('forum_post');
    }
}
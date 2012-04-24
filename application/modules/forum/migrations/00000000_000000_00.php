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
                `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
                `title` TEXT NOT NULL,
                `body` TEXT NOT NULL,
                `categoryId` INT(11) NOT NULL,
                `userId` INT(11) NOT NULL,
                `status` ENUM('active','closed','deleted') NOT NULL,
                `views` INT(11) NOT NULL DEFAULT '0',
                `comments` INT(11) NOT NULL DEFAULT '0',
                `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                `updated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY (`id`),
                KEY `FK_forum_post_to_users` (`userId`),
                KEY `FK_forum_post_to_categories` (`categoryId`),
                CONSTRAINT `FK_forum_post_to_categories` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `FK_forum_post_to_users` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=INNODB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8
            "
        );

        // category table
        $this->insert('categories', array(
            'title'       =>  'forum',
            'description' =>  'forum',
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
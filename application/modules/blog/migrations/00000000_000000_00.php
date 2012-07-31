<?php

class Blog_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // dependencies
        $this->getMigrationManager()->up('categories');
        $this->getMigrationManager()->up('comments');

        // post table
        $this->query(
            "CREATE TABLE `blog_post` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `alias` varchar(255) NOT NULL,
              `title` varchar(512) NOT NULL,
              `teaser` varchar(512) DEFAULT NULL,
              `body` text NOT NULL,
              `categoryId` int(10) unsigned NOT NULL,
              `userId` bigint(20) unsigned NOT NULL,
              `status` enum('published','draft','deleted') NOT NULL DEFAULT 'draft'
                COMMENT 'Defined in Blog_Model_Post',
              `views` int(10) NOT NULL DEFAULT '0',
              `comments` int(10) NOT NULL DEFAULT '0',
              `replies` int(10) NOT NULL DEFAULT '0',
              `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              `published` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              PRIMARY KEY (`id`),
              UNIQUE KEY `blog_post_alias` (`alias`),
              KEY `FK_blog_post_to_users` (`userId`),
              KEY `FK_blog_post_categories` (`categoryId`),
              CONSTRAINT `FK_blog_post_categories`
                FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `FK_blog_post_to_users`
                FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
        );

        // category table
        $this->insert(
            'categories', array(
                'title'       => 'Blog',
                'description' =>  '<strong>Blog module</strong>',
                'alias'       => 'blog',
                'path'        => 'blog')
        );

        // comment_aliases table
        $this->insert(
            'comment_aliases', array(
                'alias'        => 'blog',
                'options'      => '["keyRequired", "preModerationRequired"]',
                'countPerPage' => 5,
                'relatedTable' => 'blog_post')
        );
    }

    public function down()
    {
        $this->query('DELETE FROM `categories` WHERE `alias` = ?', array('blog'));
        $this->query('DELETE FROM `comment_aliases` WHERE `alias` = ?', array('blog'));

        $this->dropTable('blog_post');
    }
}


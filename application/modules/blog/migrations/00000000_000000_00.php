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
             `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
             `alias` VARCHAR(250) NOT NULL,
             `title` VARCHAR(500) NOT NULL,
             `teaser` VARCHAR(500) DEFAULT NULL,
             `body` TEXT NOT NULL,
             `categoryId` INT(10) NOT NULL,
             `userId` INT(10) NOT NULL,
             `status` ENUM('published','draft','deleted') NOT NULL DEFAULT 'draft',
             `views` INT(10) NOT NULL DEFAULT '0',
             `replies` INT(10) NOT NULL DEFAULT '0',
             `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
             `updated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
             `published` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
             PRIMARY KEY (`id`)
           ) ENGINE=INNODB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8"
        );

        // category table
        $this->insert('categories', array(
                    'title'       => 'blog',
                    'description' => 'blog',
                    'alias'       => 'blog',
                    'path'        => 'blog',
        ));

        // comment_aliases table
        $this->insert('comment_aliases', array(
                    'alias'        => 'blog',
                    'options'      => '["keyRequired", "preModerationRequired"]',
                    'countPerPage' => 5,
                    'relatedTable' => 'blog_post',
        ));
    }

    public function down()
    {
        $this->query('DELETE FROM `categories` WHERE `alias` = ?', array('blog'));
        $this->query('DELETE FROM `comment_aliases` WHERE `alias` = ?', array('blog'));

        $this->dropTable('blog_post');
    }
}


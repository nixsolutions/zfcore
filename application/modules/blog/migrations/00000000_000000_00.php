<?php

class Blog_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
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

        $this->query(
            "CREATE TABLE `blog_comment` (
            `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `postId` INT(10) NOT NULL,
            `userId` INT(10) NOT NULL,
            `body` TEXT,
            `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            `updated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (`id`)
          ) ENGINE=INNODB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8"
        );
    }

    public function down()
    {
        $this->dropTable('blog_post');
        $this->dropTable('blog_comment');
    }
}


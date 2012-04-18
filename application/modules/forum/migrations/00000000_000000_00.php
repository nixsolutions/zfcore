<?php

class Forum_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // post table
        $this->query(
            "CREATE TABLE `forum_post` (
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
              PRIMARY KEY (`id`)
            ) ENGINE=INNODB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8
            "
        );
    }

    public function down()
    {
        $this->dropTable('forum_post');
    }
}
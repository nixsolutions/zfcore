<?php

class Comments_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // create comments table
        $this->query(
            "
            CREATE TABLE `comment_aliases` (
                `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `alias` VARCHAR(255) NOT NULL,
                `options` TEXT,
                `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                `updated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                `countPerPage` SMALLINT(5) DEFAULT '10',
                `relatedTable` VARCHAR(64) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=INNODB DEFAULT CHARSET=utf8;

            CREATE TABLE `comments` (
                `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `aliasId` INT(10) NOT NULL,
                `key` VARCHAR(32) DEFAULT NULL,
                `userId` INT(10) NOT NULL,
                `parentId` INT(10) DEFAULT NULL,
                `title` VARCHAR(250) DEFAULT NULL,
                `body` TEXT,
                `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                `updated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                `status` ENUM('active','deleted','review') NOT NULL DEFAULT 'active',
                PRIMARY KEY (`id`)
            ) ENGINE=INNODB DEFAULT CHARSET=utf8;
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


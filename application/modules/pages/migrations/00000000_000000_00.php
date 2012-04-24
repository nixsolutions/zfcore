<?php

class Pages_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // static pages table
        $this->query(
            "
            CREATE TABLE `pages` (
              `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
              `pid` BIGINT(20) NOT NULL,
              `title` TEXT NOT NULL,
              `alias` VARCHAR(255) NOT NULL,
              `content` LONGTEXT,
              `keywords` TEXT,
              `description` TEXT,
              `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
              `userId` BIGINT(20) NOT NULL DEFAULT '1',
              PRIMARY KEY  (`id`,`pid`),
              UNIQUE KEY `unique` (`pid`,`alias`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
        );
    }

    public function down()
    {
        $this->dropTable('pages');
    }
}


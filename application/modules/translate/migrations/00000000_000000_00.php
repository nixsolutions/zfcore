<?php

class Translate_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // post table
        $this->query(
            "CREATE TABLE `translate` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `key` text NOT NULL,
              `value` text NOT NULL,
              `locale` varchar(2) NOT NULL DEFAULT 'en',
              `module` varchar(250) DEFAULT 'index',
              PRIMARY KEY (`id`),
              KEY `translate_key` (`key`(128))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
        );
    }

    public function down()
    {
        $this->dropTable('translate');
    }
}


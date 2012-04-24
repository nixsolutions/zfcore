<?php

class Options_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // options table
        $this->query("
            CREATE TABLE `options` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `value` longtext NOT NULL,
              `type` enum('int','float','string','array','object') NOT NULL DEFAULT 'string',
              `namespace` varchar(64) NOT NULL DEFAULT 'default',
              PRIMARY KEY (`id`,`name`,`namespace`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
        ");
    }

    public function down()
    {
        $this->dropTable('options');
    }
}


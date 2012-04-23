<?php

class Translate_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // post table
        $this->query(
            "CREATE TABLE `translate` (
             `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
             `key` TEXT NOT NULL,
             `value` TEXT NOT NULL,
             `locale` VARCHAR(2) DEFAULT 'en',
             `module` VARCHAR(250) DEFAULT 'default',
             PRIMARY KEY (`id`)
            ) ENGINE=MYISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8"
        );
    }

    public function down()
    {
        $this->dropTable('translate');
    }
}


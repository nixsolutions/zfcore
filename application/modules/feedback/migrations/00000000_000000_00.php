<?php

class Feedback_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // feedback table

        $this->query(
            "
                    CREATE TABLE `feedback` (
                      `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
                      `sender` TEXT NOT NULL,
                      `subject` TEXT NOT NULL,
                      `message` TEXT NOT NULL,
                      `email` VARCHAR(320) NOT NULL,
                      `status` ENUM('New','Read','Reply','Edit','Delete') NOT NULL DEFAULT 'New',
                      `created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                      `updated` TIMESTAMP NULL DEFAULT '2000-01-01 00:00:00',
                      `data` TEXT,
                      PRIMARY KEY  (`id`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8"
        );
    }

    public function down()
    {
        $this->dropTable('feedback');
    }
}


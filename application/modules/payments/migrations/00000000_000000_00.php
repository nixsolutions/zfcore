<?php

class Payments_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // orders table
        $this->query(
            "CREATE TABLE `orders` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `userId` bigint(20) unsigned NOT NULL,
              `created` datetime DEFAULT NULL,
              `amount` decimal(20,3) DEFAULT NULL,
              `status` enum('waiting','complete','canceled') COLLATE utf8_unicode_ci DEFAULT 'waiting',
              `paidDate` datetime DEFAULT NULL,
              `transactionId` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `FK_payments_to_users` (`userId`),
              CONSTRAINT `FK_payments_to_users` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
        );
    }

    public function down()
    {
        $this->dropTable('orders');
    }
}


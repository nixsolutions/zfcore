<?php

class Subscriptions_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // Create subscription_plans table
        $this->query("
            CREATE TABLE `subscription_plans`(
                `id` int(10) NOT NULL  auto_increment ,
                `name` varchar(40) COLLATE utf8_unicode_ci NULL  ,
                `type` enum('trial','infinite','free','monthly') COLLATE utf8_unicode_ci NULL  DEFAULT 'trial' ,
                `description` text COLLATE utf8_unicode_ci NULL  ,
                `price` float NULL  DEFAULT 0 COMMENT 'Ex . 15.50' ,
                `period` int(11) NULL  DEFAULT 30 COMMENT 'in days' ,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';
        ");


        /*Data for the table `subscription_plans` */
        $this->query("INSERT  INTO `subscription_plans`(`id`,`name`,`type`,`description`,`price`,`period`) values (1,'Trial','trial','Free on 30 days',0,30);");
        $this->query("INSERT  INTO `subscription_plans`(`id`,`name`,`type`,`description`,`price`,`period`) values (2,'Free','free','Try a stripped-down functionality',0,0);");
        $this->query("INSERT  INTO `subscription_plans`(`id`,`name`,`type`,`description`,`price`,`period`) values (3,'Monthly','monthly','Full functionality $10/month',10,30);");
        $this->query("INSERT  INTO `subscription_plans`(`id`,`name`,`type`,`description`,`price`,`period`) values (4,'Infinite','infinite','Full functionality for one life',50,0);");


        // Create subscriptions table
        $this->query("
            CREATE TABLE `subscriptions`(
                `id` bigint(20) NOT NULL  auto_increment ,
                `userId` bigint(20) unsigned NOT NULL  ,
                `subscriptionPlanId` int(10) NULL  ,
                `orderId` bigint(20) NULL  ,
                `expirationDate` datetime NULL  ,
                `paypalSubscrId` varchar(20) COLLATE utf8_unicode_ci NULL  ,
                `status` enum('active','inactive','canceled') COLLATE utf8_unicode_ci NULL  DEFAULT 'active' ,
                `updated` datetime NULL  ,
                `created` datetime NULL  ,
                PRIMARY KEY (`id`) ,
                KEY `FK_subscriptions_to_subscription_plans`(`subscriptionPlanId`) ,
                KEY `FK_subscriptions_to_users`(`userId`) ,
                KEY `FK_subscriptions_to_orders`(`orderId`) ,
                CONSTRAINT `FK_subscriptions_to_orders`
                FOREIGN KEY (`orderId`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
                CONSTRAINT `FK_subscriptions_to_subscription_plans`
                FOREIGN KEY (`subscriptionPlanId`) REFERENCES `subscription_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ,
                CONSTRAINT `FK_subscriptions_to_users`
                FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_unicode_ci';
        ");
    }


    public function down()
    {
        /* Drop subscriptions */
        $this->dropTable('subscriptions');

        /* Drop subscription_plans */
        $this->dropTable('subscription_plans');
    }
}


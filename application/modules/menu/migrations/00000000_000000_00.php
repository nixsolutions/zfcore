<?php
class Menu_Migration_00000000_000000_00 extends Core_Migration_Abstract
{

    public function up()
    {
        // menu table
        $this->query(
            "CREATE TABLE `menu` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `label` VARCHAR(255) NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `type` ENUM('uri','mvc') DEFAULT 'uri',
                `params` TEXT,
                `parentId` INT(10) UNSIGNED DEFAULT '0',
                `position` INT(11) NOT NULL DEFAULT '0',
                  `route` VARCHAR(255) DEFAULT NULL,
                `uri` VARCHAR(255) DEFAULT NULL,
                `class` VARCHAR(255) DEFAULT NULL,
                `target` ENUM('','_blank','_parent','_self','_top') DEFAULT '',
                `active` TINYINT(1) DEFAULT '0',
                `visible` TINYINT(1) DEFAULT '1',
                `routeType` VARCHAR(40) DEFAULT NULL,
                `module` VARCHAR(40) DEFAULT NULL,
                `controller` VARCHAR(40) DEFAULT NULL,
                `action` VARCHAR(40) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `parentId` (`parentId`)
            ) ENGINE=MYISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 "
        );


        $this->query(
            "Insert  into `menu`
                (`id`,`label`,`title`,`type`,`params`,`parentId`,`position`,`route`,`uri`,`class`,`target`,`active`,`visible`,`routeType`,`module`,`controller`,`action`)
            values
                (1,'Home','home','uri','{\"uri\":\"\\/\",\"visible\":\"1\",\"active\":\"0\"}',0,0,NULL,'/',NULL,'',0,1,NULL,NULL,NULL,NULL),
                (2,'Registration','register','mvc','{\"type\":\"bot\"}',1,9,'default',NULL,'register','_parent',0,1,'module','users','register','index'),
                (3,'Forget Password','Forget Password','mvc','',2,1,'forgetPassword',NULL,NULL,'',0,1,'static','users','register','forget-password'),
                (4,'Login','login','mvc','',1,2,'login',NULL,'login','',0,1,'static','users','login','index'),
                (5,'Forum','Forum','mvc','',1,3,'default',NULL,'Forum','',0,1,'module','forum','index','index'),
                (30,'Sitemap','sitemap','mvc','',1,4,'sitemap',NULL,'sitemap','',0,1,'static','pages','index','sitemap'),
                (35,'logout','logout','mvc','',1,7,'logout',NULL,'logout','',0,1,'static','users','login','logout'),
                (34,'Admin panel','Admin panel','mvc','',1,1,'default',NULL,'adminka','',0,1,'module','admin','index','index'),
                (27,'About','about','mvc','{\"alias\":\"about\"}',1,5,'pages',NULL,'about','_self',0,1,'regex','pages','index','index'),
                (41,'test','test','mvc','{\"alias\":\"test\"}',27,1,'pages',NULL,'test','',0,1,'regex','pages','index','index'),
                (39,'Faq','Faq','mvc','{\"alias\":\"faq\"}',1,8,'pages',NULL,'Faq','',0,1,'regex','pages','index','index');"
        );
    }

    public function down()
    {
        $this->dropTable('menu');
    }
}
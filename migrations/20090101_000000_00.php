<?php

class Migration_20090101_000000_00 extends Core_Migration_Abstract
{
    public function up()
    {
        // users table
        $this->query("
            CREATE TABLE `users` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `login` varchar(255) NOT NULL,
              `email` varchar(255) NOT NULL,
              `password` varchar(64) NOT NULL,
              `salt` varchar(32) NOT NULL,
              `firstname` varchar(255) DEFAULT NULL,
              `lastname` varchar(255) DEFAULT NULL,
              `avatar` varchar(512) DEFAULT NULL,
              `role` enum('guest','user','admin') NOT NULL DEFAULT 'guest',
              `status` enum('active','blocked','registered','removed') NOT NULL DEFAULT 'registered',
              `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
              `logined` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
              `ip` int(11) DEFAULT NULL,
              `count` int(11) NOT NULL DEFAULT '1',
              `hashCode` varchar(32) DEFAULT NULL,
              `inform` enum('true','false') NOT NULL DEFAULT 'false',
              `fbUid` varchar(250) DEFAULT NULL COMMENT 'facebook ID',
              `twId` varchar(250) DEFAULT NULL COMMENT 'twitter ID',
              `gId` varchar(250) DEFAULT NULL COMMENT 'google ID',
              PRIMARY KEY (`id`),
              UNIQUE KEY `unique_login` (`login`),
              UNIQUE KEY `unique_email` (`email`),
              UNIQUE KEY `activate` (`hashCode`)
            ) ENGINE=InnoDB AUTO_INCREMENT=10066 DEFAULT CHARSET=utf8
        ");

        $this->query("CREATE TABLE `mail_templates` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `description` varchar(255) DEFAULT NULL,
          `subject` text,
          `bodyHtml` text NOT NULL,
          `bodyText` text NOT NULL,
          `alias` varchar(255) NOT NULL,
          `fromEmail` varchar(255) DEFAULT NULL,
          `fromName` varchar(255) DEFAULT NULL,
          `signature` enum('true','false') NOT NULL DEFAULT 'true',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8
        ");

        $this->insert('mail_templates', array(
            'alias'   =>  'registration',
            'subject' =>  'Registration on %host%',
            'description' => 'User registration letter',
            'bodyHtml'    =>  'Please, confirm your registration<br/><br/>'.
                          'Click the folowing link:<br/>'.
                          '<a href="http://%host%/users/register/confirm-registration/hash/%hash%">http://%host%/users/register/confirm-registration/hash/%hash%</a>'.
                          '<br />'.
                          'With best regards,<br />'.
                          '<a href="http://%host%/>%host% team</a>',
            'bodyText' =>  'Please confirm your registration\n\n'.
                          'Open the folowing link in your browser: \n'.
                          'http://%host%/users/register/confirm-registration/hash/%hash%'.
                          "\n\n\n".
                          "With best regards,\n".
                          "%host% team",
            'signature' => 'true'
        ));
        $this->insert('mail_templates', array(
            'alias'   =>  'forgetPassword',
            'subject' =>  'Forget password on %host%',
            'description' => 'User forget password letter',
            'bodyHtml'    =>  'You\'re ask to reset your password.<br/><br/>'.
                          'Please confirm that you wish to reset it clicking on the url:<br />'.
                          '<a href="http://%host%/users/login/recover-password/hash/%hash%/">http://%host%/users/login/recover-password/hash/%hash%/</a><br/><br/>'.
                          'If this message was created due to mistake, you can cancel password reset via next link:<br />'.
                          '<a href="http://%host%/users/login/cancel-password-recovery/hash/%hash%/">
http://%host%/users/login/cancel-password-recovery/hash/%hash%/</a>'.
                          '<br />'.
                          'With best regards,<br />'.
                          '<a href="http://%host%/>%host% team</a>',
            'bodyText' =>  'You\'re ask to reset your password.\n\n'.
                          'Please confirm that you wish to reset it clicking on the url:\n'.
                          'http://%host%/users/login/recover-password/hash/%hash%/\n\n'.
                          'If this message was created due to mistake, you can cancel password reset via next link:\n'.
                          'http://%host%/users/login/cancel-password-recovery/hash/%hash%/'.
                          "\n\n\n".
                          "With best regards,\n".
                          "%host% team",
            'signature' => 'true'
        ));


        $this->insert('mail_templates', array(
            'alias'   =>  'newPassword',
            'subject' =>  'New password for %host%',
            'description' => '',
            'bodyHtml'    =>  'You\'re ask to reset your password.<br/><br/>'.
                          'Your new password is:<br />'.
                          '<b>%password%</b>'.
                          '<br />'.
                          'With best regards,<br />'.
                          '<a href="http://%host%/">%host% team</a>',
            'bodyText' =>  "You're ask to reset your password.\n\n".
                          "Your new password is:\n".
                          "%password%".
                          "\n\n\n".
                          "With best regards,\n".
                          "%host% team",
            'signature' => 'true'
        ));


        $this->insert('mail_templates', array(
            'alias'   =>  'reply',
            'subject' =>  'Thank you for your letter',
            'bodyHtml'    =>  'Thank you for your letter!'.
                          '<br />'.
                          'With best regards,<br />'.
                          '<a href="http://%host%/">%host% team</a>',
            'bodyText' =>  "Thank you for your letter!".
                          "\n\n\n".
                          "With best regards,\n".
                          "%host% team",
            'signature' => 'true'
        ));
    }

    public function down()
    {
        $this->dropTable('users');
        $this->dropTable('mail_templates');
    }
}


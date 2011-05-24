<?php

class Migration_20090101_000000_00 extends Core_Migration_Abstract
{
    public function up()
    {
        // users table
        $this->createTable('users');
        
        $this->createColumn('users', 
                            'login', 
                            Core_Migration_Abstract::TYPE_VARCHAR, 
                            255, null, true);
                            
        $this->createColumn('users', 
                            'email', 
                            Core_Migration_Abstract::TYPE_VARCHAR, 
                            255, null, true);
                            
        $this->createColumn('users', 
                            'password', 
                            Core_Migration_Abstract::TYPE_VARCHAR, 
                            64, null, true);
                            
        $this->createColumn('users', 
                            'salt', 
                            Core_Migration_Abstract::TYPE_VARCHAR, 
                            32, null, true);
                            
        $this->createColumn('users', 
                            'firstname', 
                            Core_Migration_Abstract::TYPE_VARCHAR, 
                            255);
                            
        $this->createColumn('users', 
                            'lastname', 
                            Core_Migration_Abstract::TYPE_VARCHAR, 
                            255);
                            
        $this->createColumn('users', 
                            'avatar', 
                            Core_Migration_Abstract::TYPE_VARCHAR, 
                            512);
                            
        $this->createColumn('users', 
                            'role', 
                            Core_Migration_Abstract::TYPE_ENUM, 
                            array('guest','user','admin'), 'guest', true);
                            
        $this->createColumn('users', 
                            'status', 
                            Core_Migration_Abstract::TYPE_ENUM,
                            array('active','blocked','registered', 'removed'), 'registered', true);
                            
        $this->createColumn('users', 
                            'created', 
                            Core_Migration_Abstract::TYPE_TIMESTAMP,
                            null,
                            'CURRENT_TIMESTAMP', true);
                            
        $this->createColumn('users', 
                            'updated', 
                            Core_Migration_Abstract::TYPE_TIMESTAMP,
                            null,
                            '2000-01-01 00:00', true);
                            
        $this->createColumn('users', 
                            'logined', 
                            Core_Migration_Abstract::TYPE_TIMESTAMP,
                            null,
                            '2000-01-01 00:00', true);
                            
        $this->createColumn('users', 
                            'ip', 
                            Core_Migration_Abstract::TYPE_INT, 
                            null);
                            
        $this->createColumn('users', 
                            'count', 
                            Core_Migration_Abstract::TYPE_INT, 
                            null, '1', true);

        $this->createColumn('users',
                            'hashCode',
                            Core_Migration_Abstract::TYPE_VARCHAR,
                            32);
                            
        $this->createColumn('users',
                            'inform',
                            Core_Migration_Abstract::TYPE_ENUM,
                            array('true','false'), 'false', true);
                            
        $this->createTable('mail_templates');

        $this->createColumn('mail_templates',
                            'description',
                            Core_Migration_Abstract::TYPE_VARCHAR,
                            255);

        $this->createColumn('mail_templates',
                            'subject',
                            Core_Migration_Abstract::TYPE_TEXT);

        $this->createColumn('mail_templates',
                            'bodyHtml',
                            Core_Migration_Abstract::TYPE_TEXT,
                            null, null, true);

        $this->createColumn('mail_templates',
                            'bodyText',
                            Core_Migration_Abstract::TYPE_TEXT,
                            null, null, true);

        $this->createColumn('mail_templates',
                            'alias',
                            Core_Migration_Abstract::TYPE_VARCHAR,
                            null, null, true);

        $this->createColumn('mail_templates',
                            'fromEmail',
                            Core_Migration_Abstract::TYPE_VARCHAR,
                            255);

        $this->createColumn('mail_templates',
                            'fromName',
                            Core_Migration_Abstract::TYPE_VARCHAR,
                            255);

        $this->createColumn('mail_templates',
                            'signature',
                            Core_Migration_Abstract::TYPE_ENUM,
                            array('true','false'), 'true', true);

        $this->createUniqueIndexes('users', array('login'), 'unique_login');
        $this->createUniqueIndexes('users', array('email'), 'unique_email');
        $this->createUniqueIndexes('users', array('hashCode'), 'activate');
        
        $this->insert('mail_templates', array(
            'alias'   =>  'registration',
            'subject' =>  'Registration on %host%',
            'description' => 'User registration letter',
            'body'    =>  'Please, confirm your registration<br/><br/>'.
                          'Click the folowing link:<br/>'.
                          '<a href="http://%host%/users/register/confirm-registration/hash/%hash%">http://%host%/users/register/confirm-registration/hash/%hash%</a>'.
                          '<br />'.
                          'With best regards,<br />'.
                          '<a href="http://%host%/>%host% team</a>',   
            'altBody' =>  'Please confirm your registration\n\n'.
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
            'body'    =>  'You\'re ask to reset your password.<br/><br/>'.
                          'Please confirm that you wish to reset it clicking on the url:<br />'.
                          '<a href="http://%host%/users/register/forget-password-confirm/hash/%hash%/confirmReset/yes">http://%host%/users/register/forget-password-confirm/hash/%hash%/confirmReset/yes</a><br/><br/>'.
                          'If this message was created due to mistake, you can cancel password reset via next link:<br />'.
                          '<a href="http://%host%/users/register/forget-password-confirm/hash/%hash%/confirmReset/no">
http://%host%/users/register/forget-password-confirm/hash/%hash%/confirmReset/no</a>'.
                          '<br />'.
                          'With best regards,<br />'.
                          '<a href="http://%host%/>%host% team</a>',
            'altBody' =>  'You\'re ask to reset your password.\n\n'.
                          'Please confirm that you wish to reset it clicking on the url:\n'.
                          'http://%host%/users/register/forget-password-confirm/hash/%hash%/confirmReset/yes\n\n'.
                          'If this message was created due to mistake, you can cancel password reset via next link:\n'.
                          'http://%host%/users/register/forget-password-confirm/hash/%hash%/confirmReset/no'.
                          "\n\n\n".
                          "With best regards,\n".
                          "%host% team",
            'signature' => 'true'
        ));
        
        
        $this->insert('mail_templates', array(
            'alias'   =>  'newPassword',
            'subject' =>  'New password for %host%',
            'description' => '',
            'body'    =>  'You\'re ask to reset your password.<br/><br/>'.
                          'Your new password is:<br />'.
                          '<b>%password%</b>'.
                          '<br />'.
                          'With best regards,<br />'.
                          '<a href="http://%host%/>%host% team</a>',
            'altBody' =>  "You're ask to reset your password.\n\n".
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
            'body'    =>  'Thank you for your letter!'.
                          '<br />'.
                          'With best regards,<br />'.
                          '<a href="http://%host%/>%host% team</a>',
            'altBody' =>  "Thank you for your letter!".
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


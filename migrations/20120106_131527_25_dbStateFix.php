<?php

class Migration_20120106_131527_25_dbStateFix extends Core_Migration_Abstract
{

    public function up()
    {
        // upgrade

        $this->query(
            "   DROP PROCEDURE IF EXISTS migrationsTableFix;
                CREATE PROCEDURE migrationsTableFix()
                BEGIN
                    IF NOT EXISTS( (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
                        AND COLUMN_NAME='db_state' AND TABLE_NAME='migrations') ) THEN
                        ALTER TABLE migrations ADD `db_state` LONGTEXT;
                    END IF;
                END;
                CALL migrationsTableFix();
                DROP PROCEDURE IF EXISTS migrationsTableFix;
        ");


    }

    public function down()
    {
        // degrade
        $this->query(
            "
                DROP PROCEDURE IF EXISTS migrationsTableFix;
                CREATE PROCEDURE migrationsTableFix()
                BEGIN
                    IF EXISTS( (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
                        AND COLUMN_NAME='db_state' AND TABLE_NAME='migrations') ) THEN
                        ALTER TABLE migrations DROP COLUMN `db_state`;
                    END IF;
                END;
                CALL migrationsTableFix();
                DROP PROCEDURE IF EXISTS migrationsTableFix;
        ");
    }

    public function getDescription()
    {
        return 'Add column db_state to migrations table (if not exist)';
    }


}


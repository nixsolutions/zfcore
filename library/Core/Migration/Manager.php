<?php
/**
 * Copyright (c) 2012 by PHP Team of NIX Solutions Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Class Core_Migration_Manager
 *
 * Migration manager
 *
 * @category Core
 * @package  Core_Migration
 *
 * @author   Anton Shevchuk <AntonShevchuk@gmail.com>
 * @link     http://anton.shevchuk.name
 */
class Core_Migration_Manager
{
    /**
     * Variable contents options
     *
     * @var array
     */
    protected $_options = array(
        // Migrations schema table name
        'migrationsSchemaTable' => 'migrations',
        // Path to project directory
        'projectDirectoryPath' => null,
        // Path to modules directory
        'modulesDirectoryPath' => null,
        // Migrations directory name
        'migrationsDirectoryName' => 'migrations',
    );

    /**
     * Message stack
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Check before start transaction
     *
     * @var bool
     */
    protected $_transactionFlag = false;

    /**
     * Constructor of Core_Migration_Manager
     *
     * @access  public
     * @param   array $options
     */
    public function __construct($options = array())
    {
        if ($options) {
            $this->_options = array_merge($this->_options, $options);
        }

        $this->_init();
    }

    /**
     * Method initialize migration schema table
     */
    protected function _init()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `" . $this->getMigrationsSchemaTable() . "`(
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `module` varchar(128) NOT NULL,
                `migration` varchar(64) NOT NULL,
                `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `state` longtext,
                PRIMARY KEY (`id`),
                UNIQUE KEY `UNIQUE_MIGRATION` (`module`,`migration`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
        ";
        Zend_Db_Table::getDefaultAdapter()->query($sql);
    }

    /**
     * Method return application directory path
     *
     * @throws Core_Exception
     * @return string
     */
    public function getProjectDirectoryPath()
    {
        if (null == $this->_options['projectDirectoryPath']) {
            throw new Core_Exception('Project directory path undefined.');
        }

        return $this->_options['projectDirectoryPath'];
    }

    /**
     * Method set application directory path
     *
     * @param  string $value
     * @return Core_Migration_Manager
     */
    public function setProjectDirectoryPath($value)
    {
        $this->_options['projectDirectoryPath'] = $value;
        return $this;
    }

    /**
     * Method return application directory path
     *
     * @throws Core_Exception
     * @return string
     */
    public function getModulesDirectoryPath()
    {
        if (null == $this->_options['modulesDirectoryPath']) {
            throw new Core_Exception('Modules directory path undefined.');
        }

        return $this->_options['modulesDirectoryPath'];
    }

    /**
     * Method set application directory path
     *
     * @param string $value
     * @return Core_Migration_Manager
     */
    public function setModulesDirectoryPath($value)
    {
        $this->_options['modulesDirectoryPath'] = $value;
        return $this;
    }

    /**
     * Method return application directory path
     *
     * @throws Core_Exception
     * @return string
     */
    public function getMigrationsDirectoryName()
    {
        if (null == $this->_options['migrationsDirectoryName']) {
            throw new Core_Exception('Migrations directory name undefined.');
        }

        return $this->_options['migrationsDirectoryName'];
    }

    /**
     * Method returns path to migrations directory
     *
     * @param string $module Module name
     * @throws Core_Exception
     * @return string
     */
    public function getMigrationsDirectoryPath($module = null)
    {
        if (null == $module) {
            $path = $this->getProjectDirectoryPath();
            $path .= '/' . $this->getMigrationsDirectoryName();
        } else {
            $modulePath = $this->getModulesDirectoryPath() . '/' . $module;

            if (!file_exists($modulePath))
                throw new Core_Exception("Module `$module` not exists.");

            $path = $modulePath . '/' . $this->getMigrationsDirectoryName();
        }

        $this->_preparePath($path);

        return $path;
    }

    /**
     * Method prepare path (create not existing dirs)
     *
     * @param string $path
     */
    protected function _preparePath($path)
    {
        if (!is_dir($path)) {
            $this->_preparePath(dirname($path));
            mkdir($path, 0777);
        }
    }

    /**
     * Method return migrations schema table
     *
     * @return string
     */
    public function getMigrationsSchemaTable()
    {
        return $this->_options['migrationsSchemaTable'];
    }

    /**
     * Method returns array of exists in filesystem migrations
     *
     * @param string $module Module name
     * @return array
     */
    public function getExistsMigrations($module = null)
    {
        $filesDirty = scandir($this->getMigrationsDirectoryPath($module));

        $migrations = array();

        // foreach loop for $filesDirty array
        foreach ($filesDirty as $file) {
            if (preg_match('/\d{8}_\d{6}_\d{2}\.php/', $file)
                || preg_match('/\d{8}_\d{6}_\d{2}_[A-z0-9]*\.php/', $file)) {
                array_push($migrations, substr($file, 0, -4));
            }
        }

        sort($migrations);

        return $migrations;
    }

    /**
     * Method return array of loaded migrations
     *
     * @param string $module Module name
     * @return array
     */
    public function getLoadedMigrations($module = null)
    {
        $select = Zend_Db_Table::getDefaultAdapter()->select()
            ->from($this->getMigrationsSchemaTable())
            ->where("module = ?", (null === $module) ? '' : $module)
            ->order('migration ASC');

        $items = Zend_Db_Table::getDefaultAdapter()->fetchAll($select);

        $migrations = array();
        foreach ($items as $item) {
            $migrations[] = $item['migration'];
        }

        return $migrations;
    }

    /**
     * Method returns stack of messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Method returns last migration for selected module
     *
     * @param null $module
     * @throws Core_Exception
     * @return string
     */
    public function getLastMigration($module = null)
    {
        $lastMigration = null;

        try {
            $select = Zend_Db_Table::getDefaultAdapter()->select()
                ->from($this->getMigrationsSchemaTable(), array('migration'))
                ->where("module = ?", (null === $module) ? '' : $module)
                ->order('migration DESC')
                ->limit(1);

            $lastMigration
                = Zend_Db_Table::getDefaultAdapter()->fetchOne($select);

            if (empty($lastMigration)) {
                throw new Core_Exception(
                    "Not found migration version in database"
                );
            }
        } catch (Exception $e) {
            // maybe table is not exist; this is first revision
            $this->_lastMigration = '0';
        }

        return $lastMigration;
    }

    /**
     * Method create's new migration file
     *
     * @param  string $module Module name
     * @param null    $migrationBody
     * @param string  $label
     * @param string  $desc
     * @return string Migration name
     */
    public function create($module = null, $migrationBody = null, $label = '', $desc = '')
    {
        $path = $this->getMigrationsDirectoryPath($module);

        list($sec, $msec) = explode(".", microtime(true));
        $_migrationName = date('Ymd_His_') . substr($msec, 0, 2);

        if (!empty($label)) {
            $_migrationName .= '_'.$label;
        }

        // Configuring after instantiation
        $methodUp = new Zend_CodeGenerator_Php_Method();
        $methodUp->setName('up')
            ->setBody('// upgrade');

        // Configuring after instantiation
        $methodDown = new Zend_CodeGenerator_Php_Method();
        $methodDown->setName('down')
            ->setBody('// degrade');

        //add description
        if (!empty($desc)) {
            $methodDesc = new Zend_CodeGenerator_Php_Method();
            $methodDesc->setName('getDescription')
                ->setBody("return '".addslashes($desc)."'; ");
        }


        if ($migrationBody) {
            if (isset($migrationBody['up'])) {
                $upBody = '';
                foreach ($migrationBody['up'] as $query) {
                    $upBody .= '$this->query(\'' . $query . '\');' . PHP_EOL;
                }
                $methodUp->setBody($upBody);
            }
            if (isset($migrationBody['down'])) {
                $downBody = '';
                foreach ($migrationBody['down'] as $query) {
                    $downBody .= '$this->query(\'' . $query . '\');' . PHP_EOL;
                }
                $methodDown->setBody($downBody);
            }
        }


        $class = new Zend_CodeGenerator_Php_Class();
        $className = ((null !== $module) ? ucfirst($module) . '_' : '')
            . 'Migration_'
            . $_migrationName;

        $class->setName($className)
            ->setExtendedClass('Core_Migration_Abstract')
            ->setMethod($methodUp)
            ->setMethod($methodDown);

        if (isset($methodDesc)) {
            $class->setMethod($methodDesc);
        }

        $file = new Zend_CodeGenerator_Php_File();
        $file->setClass($class)
            ->setFilename($path . '/' . $_migrationName . '.php')
            ->write();

        return $_migrationName;
    }

    /**
     * get last data base state
     * @param null $module
     * @return string
     */
    protected function getLastDbState($module = null)
    {
        $lastMigration = $this->getLastMigration($module);

        $dbAdapter = Zend_Db_Table::getDefaultAdapter();

        $query = $dbAdapter->select()->from(
            $this->_options['migrationsSchemaTable'],
            array('state')
        )->where('migration=?', $lastMigration);

        if ($module)
            $query->where('module=?', $module);

        $dbState = $dbAdapter->fetchOne($query);

        return $dbState;
    }

    /**
     * execute array from string
     * @param $str
     * @return array
     */
    protected function _strToArray($str)
    {
        if (!empty($str)) {

            if (strpos($str, ',')) {
                return explode(',', $str);
            }
            return array($str);
        } else {
            return array();
        }
    }


    /**
     * get difference between current db state and last db state, after this
     * create migration with auto-generated queries
     *
     * @param null   $module
     * @param string $blacklist
     * @param string $whitelist
     * @param bool   $showDiff
     * @param string $label
     * @param string $description
     * @return array|bool|string
     */

    public function generateMigration($module=null, $blacklist = '', $whitelist = '', $showDiff=false,
                                      $label = '', $description = '')
    {

        $blkListedTables = array();
        $blkListedTables[] = $this->_options['migrationsSchemaTable'];
        $blkListedTables =array_merge($blkListedTables, $this->_strToArray($blacklist));

        $whtListedTables = array();
        $whtListedTables = array_merge($whtListedTables, $this->_strToArray($whitelist));

        $options = array();
        $options['blacklist'] = $blkListedTables;

        if (sizeof($whtListedTables) > 0) {
            $options['whitelist'] = $whtListedTables;
        }

        $currDb = new Core_Db_Database($options);

        $lastPublishedDb = new Core_Db_Database($options, false);
        $lastPublishedDb->fromString($this->getLastDbState());

        $diff = new Core_Db_Database_Diff($currDb, $lastPublishedDb);
        $difference = $diff->getDifference();

        if (!count($difference['up']) && !count($difference['down'])) {
            return false;
        } else {
            if ($showDiff) {
                return $difference;
            } else {
                return $this->create($module, $difference, $label, $description);
            }
        }
    }

    /**
     * check db state in last migration, if state is empty
     * save current db state to migration
     * @param $module
     * @return bool
     */
    public function checkState($module)
    {
        $lastMigration = $this->getLastMigration($module);
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $dbState = $this->getLastDbState($module);

        if (!$dbState) {

            $db = new Core_Db_Database();

            $dbAdapter->update(
                $this->_options['migrationsSchemaTable'],
                array('state' => $db->toString()),
                array($dbAdapter->quoteInto('migration=?', $lastMigration),
                    $dbAdapter->quoteInto('module=?', $module))
            );

            return true;
        }

        return false;
    }


    /**
     * Method upgrade all migration or migrations to selected
     *
     * @param string $module Module name
     * @param string $to     Migration name or label
     * @throws Core_Exception
     * @return
     */
    public function up($module = null, $to = null)
    {
        $lastMigration = $this->getLastMigration($module);

        if (($fullMigrationName = $this->getMigrationFullName($to, $module))) {
            $to = $fullMigrationName;
        }

        if ($to) {
            if (!self::isMigration($to)) {
                throw new Core_Exception("Migration name `$to` is not valid");
            } elseif ($lastMigration == $to) {
                throw new Core_Exception("Migration `$to` is current");
            } elseif ($lastMigration > $to) {
                throw new Core_Exception(
                    "Migration `$to` is older than current "
                    . "migration `$lastMigration`"
                );
            }
        }

        $exists = $this->getExistsMigrations($module);
        $loaded = $this->getLoadedMigrations($module);

        $ready = array_diff($exists, $loaded);

        if (sizeof($ready) == 0) {
            if ($module) {
                array_push($this->_messages, $module .': no migrations to upgrade.');
            } else {
                array_push($this->_messages, 'No migrations to upgrade.');
            }

            return;
        }

        sort($ready);

        if (($to) && (!in_array($to, $exists))) {
            throw new Core_Exception("Migration `$to` not exists");
        }

        foreach ($ready as $migration) {
            if ($migration < $lastMigration) {
                throw new Core_Exception(
                    "Migration `$migration` is conflicted"
                );
            }

            try {
                $includePath = $this->getMigrationsDirectoryPath($module)
                    . '/' . $migration . '.php';

                include_once $includePath;

                $moduleAddon = ((null !== $module) ? ucfirst($module) . '_' : '');

                $migrationClass = $moduleAddon . 'Migration_' . $migration;
                /** @var Core_Migration_Abstract $migrationObject */
                $migrationObject = new $migrationClass;
                $migrationObject->setMigrationMananger($this);

                if (!$this->_transactionFlag) {
                    $migrationObject->getDbAdapter()->beginTransaction();
                    $this->_transactionFlag = true;
                    try {
                        $migrationObject->up();
                        $migrationObject->getDbAdapter()->commit();
                    } catch (Exception $e) {
                        $migrationObject->getDbAdapter()->rollBack();
                        throw new Core_Exception($e->getMessage());
                    }
                    $this->_transactionFlag = false;
                } else {
                    $migrationObject->up();
                }

                if ($module) {
                    array_push($this->_messages, $module .": upgrade to revision `$migration`");
                } else {
                    array_push($this->_messages, "Upgrade to revision `$migration`");
                }

                $this->_pushMigration($module, $migration);

                // add db state to migration
                $db = new Core_Db_Database(array('blacklist'=>$this->_options['migrationsSchemaTable']));
                $dbAdapter = Zend_Db_Table::getDefaultAdapter();
                $dbAdapter->update(
                    $this->_options['migrationsSchemaTable'],
                    array('state' => $db->toString()),
                    array($dbAdapter->quoteInto('migration=?', $migration),
                        $dbAdapter->quoteInto('module=?', $module))
                );

            } catch (Exception $e) {
                throw new Core_Exception(
                    "Migration `$migration` return exception:\n"
                    . $e->getMessage()
                );
            }


            if (($to) && ($migration == $to)) {
                break;
            }
        }
    }

    /**
     * @param $module
     * @param $to
     * @return mixed
     * @throws Core_Exception
     */
    public function fake($module, $to)
    {
        $lastMigration = $this->getLastMigration($module);

        if ($to) {
            if (!self::isMigration($to)) {
                throw new Core_Exception("Migration name `$to` is not valid");
            } elseif ($lastMigration == $to) {
                throw new Core_Exception("Migration `$to` is current");
            }

            $exists = $this->getExistsMigrations($module);

            if (($to) && (!in_array($to, $exists))) {
                array_push($this->_messages, "Migration `$to` not exists");
                return;
            }

            $loaded = $this->getLoadedMigrations($module);

            if (($to) && (in_array($to, $loaded))) {
                array_push(
                    $this->_messages,
                    "Migration `$to` already executed"
                );
                return;
            }

            $this->_pushMigration($module, $to);
            array_push(
                $this->_messages,
                "Fake upgrade to revision `$to`"
            );

        } else {
            array_push(
                $this->_messages,
                'Need migration name for fake upgrade.'
            );
            return;
        }
    }

    /**
     * Method downgrade all migration or migrations to selected
     *
     * @param string $module Module name
     * @param int    $to     Migration name
     * @throws Core_Exception
     * @return
     */
    public function down($module, $to = null)
    {
        $lastMigration = $this->getLastMigration($module);

        if (($fullMigrationName = $this->getMigrationFullName($to, $module))) {
            $to = $fullMigrationName;
        }

        if (null !== $to) {
            if (!self::isMigration($to)) {
                throw new Core_Exception("Migration name `$to` is not valid");
            } elseif ($lastMigration == $to) {
                throw new Core_Exception("Migration `$to` is current");
            } elseif ($lastMigration < $to) {
                throw new Core_Exception(
                    "Migration `$to` is younger than current "
                    . "migration `$lastMigration`"
                );
            }
        }

        $exists = $this->getExistsMigrations($module);
        $loaded = $this->getLoadedMigrations($module);

        if (sizeof($loaded) == 0) {
            if ($module) {
                array_push($this->_messages, $module .': no migrations to degrade.');
            } else {
                array_push($this->_messages, 'No migrations to degrade.');
            }
            return;
        }

        rsort($loaded);

        if (($to) && (!in_array($to, $loaded))) {
            throw new Core_Exception("Migration `$to` not loaded");
        }

        foreach ($loaded as $migration) {

            if (($to) && ($migration == $to)) {
                break;
            }

            if (!in_array($migration, $exists)) {
                throw new Core_Exception(
                    "Migration `$migration` not exists"
                );
            }

            try {
                $includePath = $this->getMigrationsDirectoryPath($module)
                    . '/' . $migration . '.php';

                include_once $includePath;

                $moduleAddon = ((null !== $module) ? ucfirst($module) . '_' : '');

                $migrationClass = $moduleAddon . 'Migration_' . $migration;
                $migrationObject = new $migrationClass;
                /** @var Core_Migration_Abstract $migrationObject */
                $migrationObject->setMigrationMananger($this);

                if (!$this->_transactionFlag) {
                    $migrationObject->getDbAdapter()->beginTransaction();
                    $this->_transactionFlag = true;
                    try {
                        $migrationObject->down();
                        $migrationObject->getDbAdapter()->commit();
                    } catch (Exception $e) {
                        $migrationObject->getDbAdapter()->rollBack();
                        throw new Core_Exception($e->getMessage());
                    }
                    $this->_transactionFlag = false;
                } else {
                    $migrationObject->down();
                }


                if ($module) {
                    array_push($this->_messages, $module .": degrade to revision `$migration`");
                } else {
                    array_push($this->_messages, "Degrade to revision `$migration`");
                }

                $this->_pullMigration($module, $migration);
            } catch (Exception $e) {
                throw new Core_Exception(
                    "Migration `$module`.`$migration` return exception:\n"
                    . $e->getMessage()
                );
            }

            //if (!$to) { break; }
        }
    }

    /**
     * Method rollback last migration or few last migrations
     *
     * @param string $module Module name
     * @param int    $step   Steps to rollback
     * @throws Core_Exception
     * @return
     */
    public function rollback($module, $step)
    {
        $lastMigration = $this->getLastMigration($module);

        if (!is_numeric($step) || ($step <= 0)) {
            throw new Core_Exception("Step count `$step` is invalid");
        }

        $exists = $this->getExistsMigrations($module);
        $loaded = $this->getLoadedMigrations($module);

        if (sizeof($loaded) == 0) {
            array_push($this->_messages, 'No migrations to rollback.');
            return;
        }

        rsort($loaded);

        foreach ($loaded as $migration) {

            if (!in_array($migration, $exists)) {
                throw new Core_Exception(
                    "Migration `$migration` not exists"
                );
            }

            try {
                $includePath = $this->getMigrationsDirectoryPath($module)
                    . '/' . $migration . '.php';

                include_once $includePath;

                $moduleAddon = ((null !== $module) ? ucfirst($module) . '_' : '');

                $migrationClass = $moduleAddon . 'Migration_' . $migration;
                $migrationObject = new $migrationClass;

                $migrationObject->getDbAdapter()->beginTransaction();
                try {
                    $migrationObject->down();
                    $migrationObject->getDbAdapter()->commit();
                } catch (Exception $e) {
                    $migrationObject->getDbAdapter()->rollBack();
                    throw new Core_Exception($e->getMessage());
                }

                array_push($this->_messages, "Degrade migration '$migration'");

                $this->_pullMigration($module, $migration);
            } catch (Exception $e) {
                throw new Core_Exception(
                    "Migration `$module`.`$migration` return exception:\n"
                    . $e->getMessage()
                );
            }

            $step--;
            if ($step <= 0) {
                break;
            }
        }
    }

    /**
     * Method add migration to schema table
     *
     * @param string $module    Module name
     * @param string $migration Migration name
     * @return Core_Migration_Manager
     */
    protected function _pushMigration($module, $migration)
    {
        if (null === $module) {
            $module = '';
        }

        try {
            $sql = "
                INSERT INTO `" . $this->getMigrationsSchemaTable() . "`
                SET module = ?, migration = ?
            ";
            Zend_Db_Table::getDefaultAdapter()
                ->query($sql, array($module, $migration));
        } catch (Exception $e) {
            // table is not exist
        }

        return $this;
    }

    /**
     * Methos remove migration from schema table
     *
     * @param string $module    Module name
     * @param string $migration Migration name
     * @return Core_Migration_Manager
     */
    protected function _pullMigration($module, $migration)
    {
        if (null === $module) {
            $module = '';
        }

        try {
            $sql = "
                DELETE FROM `" . $this->getMigrationsSchemaTable() . "`
                WHERE module = ? AND migration = ?
            ";

            Zend_Db_Table::getDefaultAdapter()
                ->query($sql, array($module, $migration));
        } catch (Exception $e) {
            // table is not exist
        }

        return $this;
    }

    /**
     * Method check string, if string valid migration name returns true
     *
     * @param string $value String to check
     * @return boolean
     */
    public static function isMigration($value)
    {
        return ('0' == $value) || preg_match('/^\d{8}_\d{6}_\d{2}$/', $value) ||
            preg_match('/\d{8}_\d{6}_\d{2}_[A-z0-9]*$/', $value);
    }

    /**
     * @param $migrationLabel
     * @param null $module
     * @return bool
     */
    protected function getMigrationFullName($migrationLabel, $module = null )
    {
        if (preg_match('/^[A-z0-9]*$/', $migrationLabel)) {

            $existMigrations = $this->getExistsMigrations($module);

            foreach ($existMigrations as $migration) {
                if (preg_match('/^\d{8}_\d{6}_\d{2}_'.$migrationLabel.'$/', $migration)) {
                    return $migration;
                }
            }
        }

        return false;
    }
}
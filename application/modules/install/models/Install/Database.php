<?php
/**
 * Install_Model_Install_Database
 *
 * @author sm
 */
class Install_Model_Install_Database
{
    /**
     * @var array
     */
    protected $_adapters = array(
        'pdo_sqlsrv'  => 'Pdo Mssql',
        'pdo_mssql'   => 'Pdo Mssql',
        'pdo_dblib'   => 'Pdo Mssql',
        'pdo_sybase'  => 'Pdo Mssql',

        'pdo_sqlite2' => 'Pdo Sqlite',
        'pdo_sqlite'  => 'Pdo Sqlite',

        'pdo_mysql'   => 'Pdo Mysql',
        'pdo_pgsql'   => 'Pdo Pgsql',
        'pdo_oci'     => 'Pdo Oci',
        'pdo_ibm'     => 'Pdo Ibm',

        'sqlsrv'      => 'Sqlsrv',
        'oci8'        => 'Oracle',
        'ibm_db2'     => 'Db2',
        'mysqli'      => 'Mysqli',
    );

    /**
     * @var array
     */
    protected $_extensions = array(
        'pdo_sybase',
        'pdo_mssql',
        'pdo_dblib',
        'pdo_sqlsrv',

        'pdo_sqlite',
        'pdo_sqlite2',

        'pdo_mysql',
        'pdo_pgsql',
        'pdo_oci',
        'pdo_ibm',
        'sqlsrv',
        'oci8',
        'ibm_db2',
        'mysqli',
    );

    /**
     * Get adapters
     *
     * @return array
     */
    public function getAdapters()
    {
        $adapters = array();

        $loaded = array_intersect($this->_extensions, get_loaded_extensions());

        foreach ($loaded as $extension) {
            $adapters[$extension] = $this->_adapters[$extension];
        }

        return $adapters;
    }

}
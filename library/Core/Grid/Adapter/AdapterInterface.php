<?php
/**
 * Core_Grid_Adapter_AdapterInterface
 *
 * @category Core
 * @package  Core_Grid
 */
interface Core_Grid_Adapter_AdapterInterface
{
    /**
     * Constructor
     *
     * @param mixed $data
     */
    public function __construct($data);

    /**
     * order
     *
     * @param string $column
     * @param string $direction
     */
    public function order($column, $direction);

    /**
     * filter
     *
     * @param string $column
     * @param string $filter
     */
    public function filter($column, $filter);

    /**
     * get source
     *
     * @return mixed
     */
    public function getSource();
}

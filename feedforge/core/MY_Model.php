<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Joins multiple tables together when tables
     * have the same primary key and join on other
     * tables with the format table_primary
     *
     * Example: product -> type -> location
     * product.id = type.product_id
     * type.id = location.type_id
     *
     * All 3 tables would be joined together.
     *
     * @param string $start the table to start on.
     * @param string $primary the primary key name (usually 'id')
     * @param int $depth how many times to recurse down. (keeps things from getting out of hand)
     */
    protected function deep_join($start, $primary = 'id', $depth = 10)
    {
        $tables = $this->db->list_tables();
        $this->constrained_deep_join($start, $tables, $primary, $depth);
    }

    /**
     * Performs exactly like deep_join only it will only use the
     * tables specified in the tables array. Instead of all tables.
     *
     * @param string $start the table to start on.
     * @param array $tables join only the tables specified in the array if available
     * @param string $primary the primary key name (usually 'id')
     * @param int $depth how many times to recurse down. (keeps things from getting out of hand)
     */
    protected function constrained_deep_join($start, array $tables, $primary = 'id', $depth = 10)
    {
        if($depth > 0)
        {
            foreach($tables as $table)
            {
                $index = $table.'_'.$primary;
                if($this->db->field_exists($index, $start))
                {
                    $this->db->join($table, ($table.'.'.$primary.' = '.$start.'.'.$index), 'left');
                    $this->constrained_deep_join($table, $tables, $primary, $depth--);
                }
            }
        }
    }
}

?>

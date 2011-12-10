<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Field_type extends CI_Driver_library
{
    private $ci;
    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->model('field_model');
        //Load valid drivers from the database.
        $this->valid_drivers = $this->ci->field_model->get_field_types(true);
    }

    public function get_database_column_type($typeid)
    {
        $driver = $this->ci->field_model->get_field_driver($typeid);
        return $this->{$driver}->get_database_column_type();
    }

}

?>
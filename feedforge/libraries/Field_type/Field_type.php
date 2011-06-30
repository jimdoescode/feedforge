<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Field_type extends CI_Driver_library
{
    public function __construct()
    {
        $ci = get_instance();
        $ci->load->model('feed_model');
        //Load valid drivers from the database.
        $this->valid_drivers = $ci->feed_model->get_field_types(true);
    }
}

?>
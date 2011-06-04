<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//This library will do processing on date fields
class date
{
    public function __construct()
    {
        $this->ci = get_instance();    
    }
    
    public function get_database_column_type()
    {
        return array('type'=>'DATE');
    }
    
    public function display_admin($name, $value)
    {
        $this->ci->load->helper('form');
        form_input(array('name'=>$name, 'value'=>$value));
    }
    
    public function display($value, $params = array())
    {
        $this->ci->load->helper('date');
        if(array_key_exists('format', $params))$value = mdate($params['format'], $value);
        return $value;
    }
}

?>
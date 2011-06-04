<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//This library will do processing on text fields 
class large_text implements field_type
{
    public function get_database_column_type()
    {
        return array('type'=>'VARCHAR','constraint'=>1024);
    }
    
    public function display_admin($name, $value)
    {
        $this->CI->load->helper('form');
        form_textarea(array('name'=>$name, 'value'=>$value));
    }
    
    public function display($value, $params = array())
    {
        return $value;
    }
}

?>
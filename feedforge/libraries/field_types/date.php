<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//This library will do processing on date fields
class date
{
    public function get_database_column_type()
    {
        return array('type'=>'DATE');
    }
    
    public function database_preprocess($value)
    {
        $date = strtotime($value);   
        return date('Y-m-d', $date); 
    }
    
    public function display_admin_input($name)
    {
        $today = date('n/j/Y');
        return '<input type="date" name="'.$name.'" id="'.$name.'" value="'.$today.'"/>';
    }
    
    public function display_tag_value($value, $params = array())
    {
        if(array_key_exists('format', $params))$value = date($params['format'], $value);
        return $value;
    }
}

?>
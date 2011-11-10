<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//This library will do processing on text fields 
class Field_type_small_text extends CI_Driver
{
    const MAX_SIZE = 128;

    public function get_database_column_type()
    {
        return array('type'=>'VARCHAR','constraint'=>self::MAX_SIZE);
    }
    
    public function database_preprocess($value)
    {
       return $value; 
    }
    
    public function display_admin_input($name)
    {
        return '<input type="text" name="'.$name.'" id="'.$name.'" maxsize="'.self::MAX_SIZE.'"/>';
    }
    
    public function display_tag_value($value, $params = array())
    {
        return $value;
    }
}

?>
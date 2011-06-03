<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//This library will do processing on text fields 
class textfield
{
    public function render_internal($name, $value)
    {
        $this->CI->load->helper('form');
        form_input(array('name'=>$name, 'value'=>$value));
    }
    
    public function render_external($value, $params)
    {
        return $value;
    }
}

?>
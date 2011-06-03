<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//This library will do processing on date fields 
class datefield
{
    public function render_internal($name, $value)
    {
        $this->CI->load->helper('form');
        form_input(array('name'=>$name, 'value'=>$value));
    }
    
    public function render_external($value, $params)
    {
        $ci = get_instance();
        $ci->load->helper('date');
        if(array_key_exists('format', $params))$value = mdate($params['format'], $value);
        return $value;
    }
}

?>
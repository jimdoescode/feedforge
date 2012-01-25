<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Template extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('parser');
        $this->load->model('variable_model');
    }
    
    function index()
    {
        $this->execute('index');
    }
    
    function execute($path = false)
    {   
        if($path === false)$path = $this->uri->uri_string();
        $variables = $this->variable_model->get_template_variables();
        $input = $_REQUEST;
        if(!empty($input))
        {
            //We need to take out any data that is not formatted as a string
            $remove = array();
            foreach($input as $key => $value)
                if(is_array($value))array_push($remove, $key);

            if(!empty($remove))$input = array_diff_key($input, $remove);
        }
        $template = $this->parser->parse_template($path, $variables, $input);
        //Assume they are specifying a directory so add the index file
        if($template === false)$template = $this->parser->parse_template($path.'/index', $variables);
        //If we found a template show it otherwise 404 they're ass.
        if($template !== false)echo $template;
        else show_404();
    }
}

?>
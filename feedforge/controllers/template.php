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
        $template = $this->parser->parse_template($path, $variables);
        if($template !== false)echo $template;
        else show_404();
    }
}

?>
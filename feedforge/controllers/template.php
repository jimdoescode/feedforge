<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Template extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('parser');
    }
    
    function index()
    {
        $this->execute('index');
    }
    
    function execute($path = false)
    {   
        if($path === false)$path = $this->uri->uri_string();
        $template = $this->parser->parse_template($path);
        if($template !== false)echo $template;
        else show_404();
    }
}

?>
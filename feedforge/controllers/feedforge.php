<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Feedforge extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->config->load('ff');
        $this->load->library('parser', array('keys'=>$this->config->item('feed_params')));
        $this->load->model('feed_model');
    }
    
    function index()
    {
        $this->execute('index');
    }
    
    private function _make_error_template($message)
    {
        return $this->parser->parse('error', array('message'=>$message), true);
    }
    
    function execute($path = false)
    {   
        if($path === false)$path = $this->uri->uri_string();
        $template = $this->parser->parse_template($path);
        
        echo $template;
    }
}

?>
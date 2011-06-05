<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller
{
    private $messages = array();
    
    function __construct()
    {
        parent::__construct();
        $this->load->model('feed_model');    
    }
    
    private function _render($title, $page)
    {
        $messages = $this->messages;
        $this->messages = array();
        $this->load->view('admin_template', array('title'=>$title, 'page'=>$page, 'messages'=>$this->messages));
    }
    
    private function _queue_message($title, $message)
    {
        array_push($this->messages, array('title'=>$title, 'text'=>$message));
    }
    
    function index()
    {
        $this->_render('Welcome', $this->load->view('admin_home', null, true));
    }
    
    function feeds()
    {
        $feeds = $this->feed_model->get_feeds();
        $this->_render('Feeds', $this->load->view('admin_feeds', array('feeds'=>$feeds), true));
    }
    
    function add_entry()
    {
        
    }
    
    function update_entry()
    {
        
    }
    
    function delete_entry()
    {
        
    }
    
    function create_feed($feedname)
    {
        
    }
    
    function update_feed()
    {
        
    }
    
    function delete_feed()
    {
        
    }
}

?>
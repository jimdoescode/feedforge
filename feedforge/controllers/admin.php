<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller
{
    private $messages = array();
    
    function __construct()
    {
        parent::__construct();
        
        //TODO: Put a check in here to make sure if they aren't authenticated that
        //the are redirected to an authentication page.
        
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
    
    function get_feed_data($raw = false)
    {
        $feeds = $this->feed_model->get_feeds();
        if(!$raw)$feeds = json_encode(array('feeds'=>$feeds));
        return $feeds;
    }
    
    function feeds()
    {
        $feeds = $this->get_feed_data(true);
        if($feeds === false)$feeds = array();
        $this->_render('Feeds', $this->load->view('admin_feeds', array('feeds'=>$feeds), true));
    }
    
    function feed_fields($feedid)
    {
        $feed = $this->feed_model->get_feed($feedid);
        $fields = $this->feed_model->get_feed_fields($feedid);
        if($fields === false)$fields = array();
        $this->_render('Feed Fields', $this->load->view('admin_feed_fields', array('name'=>$feed['title'], 'fields'=>$fields), true));
    }
    
    function add_feed()
    {
        $title = $this->input->post('title');
        $this->feed_model->create_feed($title);
        header('application/json');
        echo $this->get_feed_data();
    }
    
    function update_feed()
    {
        $feedid = $this->input->post('id');
        $title = $this->input->post('title');
        $this->feed_model->update_feed($feedid, $title);
        header('application/json');
        echo $this->get_feed_data();
    }
    
    function delete_feed()
    {
        $feedid = $this->input->post('id');
        $this->feed_model->delete_feed($feedid);
        header('application/json');
        echo $this->get_feed_data();
    }
    
    function entries($feedshort)
    {
        
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
}

?>
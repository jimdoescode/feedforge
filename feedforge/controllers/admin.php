<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        
        //TODO: Put a check in here to make sure if they aren't authenticated that
        //the are redirected to an authentication page.
        
        $this->load->model('feed_model');    
    }
    
    private function _render($title, $page)
    {
        $this->load->view('admin_template', array('title'=>$title, 'page'=>$page));
    }
    
    function index()
    {
        $this->_render('Welcome', $this->load->view('admin_home', null, true));
    }
    
    private function _get_feed_data($raw = false)
    {
        $feeds = $this->feed_model->get_feeds();
        if(!$raw)$feeds = json_encode(array('feeds'=>$feeds));
        return $feeds;
    }
    
    function feeds()
    {
        $feeds = $this->_get_feed_data(true);
        if($feeds === false)$feeds = array();
        $this->_render('Feeds', $this->load->view('admin_feeds', array('feeds'=>$feeds), true));
    }
    
    function modify_feeds()
    {
        $feedid = $this->input->post('id');
        $title = $this->input->post('title');
        
        if(strlen(trim($title)) > 0)
        {
            if($feedid > 0)$this->feed_model->update_feed($feedid, $title);
            else $this->feed_model->create_feed($title);
        }
        header('application/json');
        echo $this->_get_feed_data();
    }
    
    function delete_feed()
    {
        $feedid = $this->input->post('id');
        $this->feed_model->delete_feed($feedid);
        header('application/json');
        echo $this->_get_feed_data();
    }
    
    private function _get_feed_field_data($feedid, $raw = false)
    {
        $feed = $this->feed_model->get_feed($feedid);
        $fields = $this->feed_model->get_feed_fields($feedid);
        if($fields === false)$fields = array();
        $types = $this->feed_model->get_field_types();
        $data = array('feed'=>$feed, 'types'=>$types, 'fields'=>$fields);
        if(!$raw)return json_encode($data);
        return $data;
    }
    
    function feed_fields($feedid)
    {
        $data = $this->_get_feed_field_data($feedid, true);
        $this->_render('Feed Fields', $this->load->view('admin_feed_fields', $data, true));
    }
    
    function modify_feed_fields($feedid)
    {
        $fieldid = $this->input->post('id');
        $title = $this->input->post('title');
        $typeid = $this->input->post('type');
        
        if(strlen(trim($title)) > 0 && strlen(trim($typeid)) > 0)
        {
            if($fieldid > 0)$this->feed_model->update_feed_field($feedid, $fieldid, $title, $typeid);
            else $this->feed_model->add_feed_field($feedid, $title, $typeid);
        }
        header('application/json');
        echo $this->_get_feed_field_data($feedid);
    }
    
    function delete_feed_field($feedid)
    {
        $fieldid = $this->input->post('id');
        $this->feed_model->delete_feed_field($feedid, $fieldid);
        header('application/json');
        echo $this->_get_feed_field_data($feedid);
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
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        
        //TODO: Put a check in here to make sure if they aren't authenticated 
        //that they are redirected to an authentication page.
        
        $this->load->model('feed_model');
        $this->load->model('variable_model');
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
    
    private function _get_feed_entries($feedid, $raw = false)
    {
        $feed = $this->feed_model->get_feed($feedid);
        $fields = $this->feed_model->get_feed_fields($feedid);
        $entries = $this->feed_model->get_feed_entries($feed['short']);
        if($entries === false)$entries = array();
        if($fields === false)$fields = array();
        
        //TODO: Add Entry Preprocessing here to display entry values consistently in the admin panel
        
        $data = array('feed'=>$feed, 'fields'=>$fields, 'entries'=>$entries);
        if(!$raw)return json_encode($data);
        return $data;
    }
    
    function feed_entries($feedid)
    {
        $data = $this->_get_feed_entries($feedid, true);
        
        $fieldcount = count($data['fields']);
        for($i=0; $i < $fieldcount; $i++)
        {
            $this->load->library('field_types/'.$data['fields'][$i]['library'], null, 'field');
            $data['fields'][$i]['input'] = $this->field->display_admin_input($data['fields'][$i]['short']);
        }
        
        $this->_render('Feed Entries', $this->load->view('admin_feed_entries', $data, true));
    }
    
    function modify_feed_entries($feedid)
    {
        $entryid = $this->input->post('id');
        //Diff the post array to prevent messing up relations via changing id values.
        $values = array_diff($_POST, array('id'=>$entryid));
        
        //Perform field preprocessing on the input
        $fields = $this->feed_model->get_feed_fields($feedid);
        foreach($fields as $field)
        {
            $this->load->library('field_types/'.$field['library'], null, 'field');
            $values[$field['short']] = $this->field->database_preprocess($values[$field['short']]);
            //Remove any curly braces out of fear they might be feedforge tags.
            $values[$field['short']] = str_replace(array('{', '}'), '', $values[$field['short']]);
        }
        
        if($entryid > 0)$this->feed_model->update_feed_entry($feedid, $entryid, $values);
        else $this->feed_model->add_feed_entry($feedid, $values);
        
        header('application/json');
        echo $this->_get_feed_entries($feedid);
    }
    
    function delete_feed_entry($feedid)
    {
        $entryid = $this->input->post('id');
        $this->feed_model->delete_feed_entry($feedid, $entryid);
        header('application/json');
        echo $this->_get_feed_entries($feedid);
    }
    
    private function _get_variable_data($raw = false)
    {
        $vars = $this->variable_model->get_variables();
        if(!$raw)$vars = json_encode(array('variables'=>$vars));
        return $vars;
    }
    
    function variables()
    {
        $vars = $this->_get_variable_data(true);
        if($vars === false)$vars = array();
        $this->_render('Variables', $this->load->view('admin_variables', array('variables'=>$vars), true));
    }
    
    function modify_variables()
    {
        $id = $this->input->post('id');
        $title = $this->input->post('title');
        $value = $this->input->post('value');
        
        if(strlen(trim($title)) > 0)
        {
            if($id > 0)$this->variable_model->update_variable($id, $title, $value);
            else $this->variable_model->add_variable($title, $value);
        }
        header('application/json');
        echo $this->_get_variable_data();
    }
    
    function delete_variable()
    {
        $id = $this->input->post('id');
        $this->variable_model->delete_variable($id);
        header('application/json');
        echo $this->_get_variable_data();
    }
}

?>
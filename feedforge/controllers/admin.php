<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        
        //TODO: Put a check in here to make sure if they aren't authenticated 
        //that they are redirected to an authentication page.
        $this->_check_encryption_key();
        
        $this->load->library('session');
        $this->load->model('feed_model');
        $this->load->model('variable_model');
    }
    
    private function _render($title, $page)
    {
        $this->load->view('admin_template', array('title'=>$title, 'page'=>$page));
    }
    
    private function _check_encryption_key()
    {
        if(strlen($this->config->item('encryption_key')) == 0)
        {
            $this->load->helper('string');
            $id = random_string('alnum', 32);
            $this->config->set_item('config', 'encryption_key', "'{$id}'");     
        }
    }
    
    private function _check_login()
    {
        if($this->_has_login())
        {
            $user = $this->session->userdata('admin_user');
            $pass = $this->session->userdata('admin_password');
            return ($user === $this->config->item('admin_user') && $pass === $this->config->item('admin_password'));
        }
        else return false;
    }
    
    private function _has_login()
    {
        $user = trim($this->config->item('admin_user'));
        $pass = trim($this->config->item('admin_password'));
        $salt = trim($this->config->item('admin_salt'));
        return (strlen($user) > 0 && strlen($pass) > 0 && strlen($salt) > 0);
    }
    
    function logout()
    {
        $this->session->unset_userdata('admin_user');
        $this->session->unset_userdata('admin_password');
        redirect('admin/login');
    }
    
    function login()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_message('required', 'Required');
        $this->form_validation->set_error_delimiters('<span class="error">', '</span>');
        $this->form_validation->set_rules('username', 'User Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

        if($this->form_validation->run())
        {
            if(!$this->_has_login())
            {
                $this->load->helper('string');
                $salt = random_string('alnum', 22);
                $pass = $this->_hash_pw($this->input->post('password'), $salt);
                $user = $this->input->post('username');
                
                $this->config->set_item('feedforge', 'admin_user', "'{$user}'");
                $this->config->set_item('feedforge', 'admin_password', "'{$pass}'");
                $this->config->set_item('feedforge', 'admin_salt', "'{$salt}'");
                
                $this->session->set_userdata(array('admin_user'=>$user, 'admin_password'=>$pass));
                redirect('admin');
            }
            else
            {
                $user = $this->input->post('username');
                $pass = $this->_hash_pw($this->input->post('password'));
                if($user == $this->config->item('admin_user') && $pass == $this->config->item('admin_password'))
                {
                    $this->session->set_userdata(array('admin_user'=>$user, 'admin_password'=>$pass));
                    redirect('admin');
                }
            }
        }
        else
        {
            if(!$this->_has_login())$this->_render('Please Create an Account', $this->load->view('admin_login', array('first_time'=>true), true));
            else $this->_render('Please Login', $this->load->view('admin_login', array('first_time'=>false), true));
        }
    }
    
    private function _hash_pw($pw, $salt = false)
    {
        if($salt === false)$salt = $this->config->item('admin_salt');
        //Hopefully they have at least one of these hashing algorithms available.
        if(defined('CRYPT_BLOWFISH') && CRYPT_BLOWFISH == 1)$pw = crypt($pw, '$2a$10$'.$salt);
        elseif(defined('CRYPT_SHA512') && CRYPT_SHA512 == 1)$pw = crypt($pw, '$6$rounds=5000$'.$salt);
        elseif(defined('CRYPT_SHA256') && CRYPT_SHA256 == 1)$pw = crypt($pw, '$5$rounds=5000$'.$salt);
        else //Pretty crappy but it is a last resort.
        {
            for($i=0; $i < 1000; $i++)
                $pw = sha1($pw.$salt);
        }
        return $pw;
    }
    
    function index()
    {
        if(!$this->_has_login() || !$this->_check_login())redirect('admin/login');
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
        if(!$this->_has_login() || !$this->_check_login())redirect('admin/login');
        $feeds = $this->_get_feed_data(true);
        if($feeds === false)$feeds = array();
        $this->_render('Feeds', $this->load->view('admin_feeds', array('feeds'=>$feeds), true));
    }
    
    function modify_feeds()
    {
        if(!$this->_has_login() || !$this->_check_login())redirect('admin/login');
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
        if(!$this->_has_login() || !$this->_check_login())redirect('admin/login');
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
        if(!$this->_has_login() || !$this->_check_login())redirect('admin/login');
        $data = $this->_get_feed_field_data($feedid, true);
        $this->_render('Feed Fields', $this->load->view('admin_feed_fields', $data, true));
    }
    
    function modify_feed_fields($feedid)
    {
        if(!$this->_has_login() || !$this->_check_login())redirect('admin/login');
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
        if(!$this->_has_login() || !$this->_check_login())redirect('admin/login');
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
        if(!$this->_has_login() || !$this->_check_login())redirect('admin/login');
        $data = $this->_get_feed_entries($feedid, true);
        
        $fieldcount = count($data['fields']);
        $this->load->driver('field_type');
        for($i=0; $i < $fieldcount; $i++)
            $data['fields'][$i]['input'] = $this->field_type->{$data['fields'][$i]['driver']}->display_admin_input($data['fields'][$i]['short']);
        
        $this->_render('Feed Entries', $this->load->view('admin_feed_entries', $data, true));
    }
    
    function modify_feed_entries($feedid)
    {
        if(!$this->_has_login() || !$this->_check_login())redirect('admin/login');
        $entryid = $this->input->post('id');
        //Diff the post array to prevent messing up relations via changing id values.
        $values = array_diff($_POST, array('id'=>$entryid));
        
        //Perform field preprocessing on the input
        $fields = $this->feed_model->get_feed_fields($feedid);
        $this->load->driver('field_type');
        foreach($fields as $field)
        {
            $values[$field['short']] = $this->field_type->{$field['driver']}->database_preprocess($values[$field['short']]);
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
        if(!$this->_has_login() || !$this->_check_login())redirect('admin/login');
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
        if(!$this->_has_login() || !$this->_check_login())redirect('admin/login');
        $vars = $this->_get_variable_data(true);
        if($vars === false)$vars = array();
        $this->_render('Variables', $this->load->view('admin_variables', array('variables'=>$vars), true));
    }
    
    function modify_variables()
    {
        if(!$this->_has_login() || !$this->_check_login())redirect('admin/login');
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
        if(!$this->_has_login() || !$this->_check_login())redirect('admin/login');
        $id = $this->input->post('id');
        $this->variable_model->delete_variable($id);
        header('application/json');
        echo $this->_get_variable_data();
    }
}

?>
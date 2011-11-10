<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Installer extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->helper('string');
        $id = random_string('alnum', 32);
        $this->config->set_item('config', 'encryption_key', "'{$id}'");
        
        $this->load->model('installer_model');
        $this->load->helper('language');
        $this->lang->load('installer');
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

    }
}
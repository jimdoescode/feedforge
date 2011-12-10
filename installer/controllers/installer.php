<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Installer extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->helper('string');
        $this->load->library('form_validation');
        $this->load->library('session');

        $this->form_validation->set_message('required', 'Required');
        $this->form_validation->set_error_delimiters('<span class="error">', '</span>');
    }

    private function _render($page)
    {
        $this->load->view('install_template', array('page'=>$page));
    }

    private function _hash_pw($pw)
    {
        $salt = random_string('alnum', 22);
        //Hopefully they have at least one of these hashing algorithms available.
        if(defined('CRYPT_BLOWFISH') && CRYPT_BLOWFISH == 1)$pw = crypt($pw, '$2a$10$'.$salt);
        elseif(defined('CRYPT_SHA512') && CRYPT_SHA512 == 1)$pw = crypt($pw, '$6$rounds=5000$'.$salt);
        elseif(defined('CRYPT_SHA256') && CRYPT_SHA256 == 1)$pw = crypt($pw, '$5$rounds=5000$'.$salt);
        else //Pretty crappy but it is a last resort.
        {
            for($i=0; $i < 1000; $i++)
                $pw = sha1($pw.$salt);
        }
        return array('password'=>$pw, 'salt'=>$salt);
    }

    private function _write_database_config($user, $password, $host, $database, $driver = 'mysql')
    {
        $str = '<?php  if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\');'.PHP_EOL.PHP_EOL;
        $str .= '$active_group = "default";'.PHP_EOL;
        $str .= '$active_record = TRUE;'.PHP_EOL.PHP_EOL;
        $str .= '$db[\'default\'][\'hostname\'] = "'.$host.'";'.PHP_EOL;
        $str .= '$db[\'default\'][\'username\'] = "'.$user.'";'.PHP_EOL;
        $str .= '$db[\'default\'][\'password\'] = "'.$password.'";'.PHP_EOL;
        $str .= '$db[\'default\'][\'database\'] = "'.$database.'";'.PHP_EOL;
        $str .= '$db[\'default\'][\'dbdriver\'] = "'.$driver.'";'.PHP_EOL;
        $str .= '$db[\'default\'][\'dbprefix\'] = "";'.PHP_EOL;
        $str .= '$db[\'default\'][\'pconnect\'] = TRUE;'.PHP_EOL;
        $str .= '$db[\'default\'][\'db_debug\'] = FALSE;'.PHP_EOL;
        $str .= '$db[\'default\'][\'cache_on\'] = FALSE;'.PHP_EOL;
        $str .= '$db[\'default\'][\'cachedir\'] = "";'.PHP_EOL;
        $str .= '$db[\'default\'][\'char_set\'] = "utf8";'.PHP_EOL;
        $str .= '$db[\'default\'][\'dbcollat\'] = "utf8_general_ci";'.PHP_EOL;
        $str .= '$db[\'default\'][\'swap_pre\'] = "";'.PHP_EOL;
        $str .= '$db[\'default\'][\'autoinit\'] = TRUE;'.PHP_EOL;
        $str .= '$db[\'default\'][\'stricton\'] = FALSE;'.PHP_EOL;

        return (file_put_contents('feedforge/config/database.php', $str) !== FALSE);
    }

    function index()
    {
        $this->_render($this->load->view('welcome', array(), true));
    }

    function step($step = 1)
    {
        if($step == 1)
        {
            $this->form_validation->set_rules('baseurl', 'Base URL', 'trim|required|xss_clean');
            $this->form_validation->set_rules('username', 'User Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
            if($this->form_validation->run())
            {
                $pwdata = $this->_hash_pw($this->input->post('password'));
                $baseurl = $this->input->post('baseurl');
                $success = true;
                $id = random_string('alnum', 32);

                if($success)$success = $this->config->replace_item('config', 'base_url', "'{$baseurl}'");
                if($success)$success = $this->config->replace_item('config', 'encryption_key', "'{$id}'");
                if($success)$success = $this->config->replace_item('feedforge', 'admin_user', "'".$this->input->post('username')."'");
                if($success)$success = $this->config->replace_item('feedforge', 'admin_password', "'{$pwdata['password']}'");
                if($success)$success = $this->config->replace_item('feedforge', 'admin_salt', "'{$pwdata['salt']}'");
                //Everything worked great so move on to step 2
                $this->session->set_userdata('baseurl', $baseurl);
                if($success)redirect($baseurl.'installer/step/2');

                else die("Could not write to config files.");
            }
            else $this->_render($this->load->view('step_1', array(), true));
        }
        elseif($step == 2)
        {
            $this->form_validation->set_rules('host', 'Host', 'trim|required|xss_clean');
            $this->form_validation->set_rules('database', 'Database', 'trim|required|xss_clean');
            $this->form_validation->set_rules('username', 'User Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
            if($this->form_validation->run())
            {
                $user = $this->input->post('username');
                $pass = $this->input->post('password');
                $host = $this->input->post('host');
                $name = $this->input->post('database');

                $success = true;
                if($success)$success = $this->_write_database_config($user, $pass, $host, $name);
                if($success)$success = $this->install_model->create_database($user, $pass, $host, $name);
                //Everything worked so take them to the finish screen
                if($success)redirect($this->session->userdata('baseurl').'installer/step/finished');
                else $this->_render($this->load->view('step_2', array(), true));;
            }
            else $this->_render($this->load->view('step_2', array(), true));
        }
        else
        {
            $this->_render($this->load->view('finish', array(), true));
        }
    }
}
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Emailer
{
    function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->library('email');
        $this->ci->load->config('emailer');
    }

    function contact($params)
    {
        $error = false;
        if($this->ci->config->item('enable_recaptcha'))
        {
            if(array_key_exists('recaptcha_challenge_field', $_POST) && array_key_exists('recaptcha_response_field', $_POST))
            {
                require_once('recaptchalib.php');
                $resp = recaptcha_check_answer ($this->ci->config->item('recaptcha_private_key'), $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
                $error = !$resp->is_valid;
            }
            else $error = true;
        }

        if(!$error)
        {
            $this->ci->email->from($this->ci->config->item('contact_from'));
            $this->ci->email->to($this->ci->config->item('contact_to'));
            if(array_key_exists('email', $_POST) && array_key_exists('message', $_POST))
            {
                $this->ci->email->subject("New message from: {$_POST['email']}");
                $this->ci->email->message($_POST['message']);
                $this->ci->email->send();
                redirect("{$_POST['success_redirect']}?message=".rawurlencode($this->ci->config->item('success_message')));
            }
            else redirect("{$_POST['error_redirect']}?error=Missing%20Fields");
        }
        else redirect("{$_POST['error_redirect']}?error=Invalid%20Captcha%20Text");
    }
}
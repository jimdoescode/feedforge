<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Module extends CI_Controller
{
    function _remap($module, $params = array())
    {
        if(!empty($params))
        {
            $method = array_shift($params);
            $this->index($module, $method, $params);
        }
        else show_404();
    }

    function index($module, $method, $params = array())
    {
        $this->load->add_package_path(APPPATH."third_party/{$module}/");
        $this->load->library($module);
        $this->{$module}->{$method}($params);
        $this->load->remove_package_path(APPPATH."third_party/{$module}/");
    }
}

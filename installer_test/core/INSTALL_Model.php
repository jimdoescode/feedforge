<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class INSTALL_Model extends CI_Model
{
    protected function _get_url_title($title)
    {
        $separator = $this->config->item('separator');
        return url_title($title, $separator, true);
    }
    
    protected function _get_short($id, $table)
    {
        $query = $this->db->select('short')->where('id', $id)->get($table);
        if($query->num_rows() > 0)return $query->row()->short;
        return false;
    }
}

?>
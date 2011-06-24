<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Variable_model extends FF_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function get_variables()
    {
        $query = $this->db->get('ff_variable');
        if($query->num_rows() > 0)return $query->result_array();
        return false;
    }
    
    public function get_template_variables()
    {
        $query = $this->db->select('short, value')->get('ff_variable');
        if($query->num_rows() > 0)
        {
            //We need to get the result array
            //into the form short => value.
            $variables = array();
            foreach($query->result() as $row)
                $variables[$row->short] = $row->value;
            return $variables;
        }
        return false;
    }
    
    public function add_variable($title, $value)
    {
        $short = $this->_get_url_title($title);
        $this->db->insert('ff_variable', array('short'=>$short, 'title'=>$title, 'value'=>$value));
    }
    
    public function update_variable($id, $title, $value)
    {
        $short = $this->_get_url_title($title);
        $this->db->where(array('id'=>$id));
        $this->db->update('ff_variable', array('short'=>$short, 'title'=>$title, 'value'=>$value));
    }
    
    public function delete_variable($id)
    {
        $this->db->delete('ff_variable', array('id' => $id));
    }
}

?>
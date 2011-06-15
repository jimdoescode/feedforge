<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Feed_model extends FF_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
    }
    /*
    private function _get_url_title($title)
    {
        $separator = $this->config->item('separator');
        return url_title($title, $separator, true);
    }
    
    private function _get_short($id, $table)
    {
        $query = $this->db->select('short')->where('id', $id)->get($table);
        if($query->num_rows() > 0)return $query->row()->short;
        return false;
    }
    */
    private function _get_field_type($id)
    {
        $query = $this->db->select('library')->where('id', $id)->get('feed_field_type');
        if($query->num_rows() > 0)return $query->row()->library;
        return false;
    }
    
    function get_feed($feedid)
    {
        $query = $this->db->get_where('feed', array('id'=>$feedid));
        if($query->num_rows() > 0)return $query->row_array();
        return false;
    }
    
    function get_feeds()
    {
        $query = $this->db->get('feed');
        if($query->num_rows() > 0)return $query->result_array();
        return false;
    }
    
    function create_feed($title)
    {
        $short = $this->_get_url_title($title);
        
        $this->db->insert('feed', array('short'=>$short, 'title'=>$title));
        $this->dbforge->add_field('id');
        $this->dbforge->create_table($short);
    }
    
    function update_feed($feedid, $title)
    {
        $feedshort = $this->_get_short($feedid, 'feed');
        $newshort = $this->_get_url_title($title);
        
        if($feedshort != $newshort)
        {
            $this->db->update('feed', array('title'=>$title, 'short'=>$newshort), array('id' => $feedid));
            $this->dbforge->rename_table($feedshort, $newshort);
        }
    }
    
    function delete_feed($feedid)
    {
        $feedshort = $this->_get_short($feedid, 'feed');
        
        $this->db->delete('feed', array('id'=>$feedid));
        $this->db->delete('feed_field', array('feed_id'=>$feedid));
        $this->dbforge->drop_table($feedshort);
    }
    
    function get_feed_fields($feedid)
    {
        $sql = 'SELECT ff.*, fft.title AS type_name, fft.library FROM feed_field ff, feed_field_type fft WHERE ff.feed_id=? AND fft.id=ff.feed_field_type_id';
        $query = $this->db->query($sql, array($feedid));
        if($query->num_rows() > 0)return $query->result_array();
        return false;
    }
    
    function add_feed_field($feedid, $title, $typeid)
    {
        $feedshort = $this->_get_short($feedid, 'feed');
        $typelib = $this->_get_field_type($typeid);
        $fieldshort = $this->_get_url_title($title);
        
        $this->db->insert('feed_field', array('feed_id'=>$feedid, 'short'=>$fieldshort, 'title'=>$title, 'feed_field_type_id'=>$typeid));
        $this->load->library('field_types/'.$typelib, null, 'field');
        $this->dbforge->add_column($feedshort, array($fieldshort => $this->field->get_database_column_type()));
    }
    
    function update_feed_field($feedid, $fieldid, $title, $typeid)
    {
        $feedshort = $this->_get_short($feedid, 'feed');
        $fieldshort = $this->_get_short($fieldid, 'feed_field');
        $typelib = $this->_get_field_type($typeid);
        $newshort = $this->_get_url_title($title);
        
        $this->db->update('feed_field', array('title'=>$title, 'short'=>$newshort, 'feed_field_type_id'=>$typeid), array('id'=>$fieldid));
        $this->load->library('field_types/'.$typelib, null, 'field');
        $fielddata = array_merge($this->field->get_database_column_type(), array('name'=>$newshort));
        $this->dbforge->modify_column($feedshort, array($fieldshort=>$fielddata));
    }
    
    function delete_feed_field($feedid, $fieldid)
    {
        $feedshort = $this->_get_short($feedid, 'feed');
        $fieldshort = $this->_get_short($fieldid, 'feed_field');
        
        $this->db->delete('feed_field', array('id' => $fieldid));
        $this->dbforge->drop_column($feedshort, $fieldshort);
    }
    
    function get_field_types()
    {
        $query = $this->db->get('feed_field_type');
        if($query->num_rows() > 0)return $query->result_array();
        return false;
    }
    
    function add_feed_entry($feedid, $entrydata)
    {
        $feedshort = $this->_get_short($feedid, 'feed');
        $this->db->insert($feedshort, $entrydata);
    }
    
    function update_feed_entry($feedid, $entryid, $entrydata)
    {
        $feedshort = $this->_get_short($feedid, 'feed');
        $this->db->update($feedshort, $entrydata, array('id'=>$entryid));
    }
    
    function delete_feed_entry($feedid, $entryid)
    {
        $feedshort = $this->_get_short($feedid, 'feed');
        $this->db->delete($feedshort, array('id' => $entryid));
    }
    
    function get_feed_entries($short)
    {
        $query = $this->db->get($short);
        if($query->num_rows() > 0)return $query->result_array();
        return false;
    }
    
    function get_feed_entry_types($feedshort)
    {
        $sql = 'SELECT ff.short, fft.library FROM feed f, feed_field ff, feed_field_type fft WHERE f.short=? AND ff.feed_id=f.id AND fft.id=ff.feed_field_type_id';
        $query = $this->db->query($sql, array($feedshort));
        
        if($query->num_rows() > 0)
        {
            $types = array();
            $results = $query->result_array();
            foreach($results as $result)
                $types[$result['short']] = $result['library'];
            return $types;
        }
        return false;
    }
    
    function get_template_feed_entries($feedshort, $options = array(), $limit = 30, $offset = 0)
    {
        $query = $this->db->get_where($feedshort, $options, $limit, $offset);
        if($query->num_rows() > 0)return $query->result_array();
        return false;
    }
}

?>
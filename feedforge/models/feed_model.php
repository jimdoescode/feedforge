<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Feed_model extends FF_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
    }

    function get_feed($feedid)
    {
        $query = $this->db->get_where('ff_feed', array('id'=>$feedid));
        if($query->num_rows() > 0)return $query->row_array();
        return false;
    }
    
    function get_feeds()
    {
        $query = $this->db->get('ff_feed');
        if($query->num_rows() > 0)return $query->result_array();
        return false;
    }
    
    function create_feed($title)
    {
        $short = $this->_get_url_title($title);
        
        $this->db->insert('ff_feed', array('short'=>$short, 'title'=>$title));
        $this->dbforge->add_field('id');
        $this->dbforge->create_table($short);
    }
    
    function update_feed($feedid, $title)
    {
        $feedshort = $this->_get_short($feedid, 'ff_feed');
        $newshort = $this->_get_url_title($title);
        
        if($feedshort != $newshort)
        {
            $this->db->update('ff_feed', array('title'=>$title, 'short'=>$newshort), array('id' => $feedid));
            $this->dbforge->rename_table($feedshort, $newshort);
        }
    }
    
    function delete_feed($feedid)
    {
        $feedshort = $this->_get_short($feedid, 'ff_feed');
        
        $this->db->delete('ff_feed', array('id'=>$feedid));
        $this->db->delete('ff_feed_field', array('feed_id'=>$feedid));
        $this->dbforge->drop_table($feedshort);
    }
    
    function get_feed_fields($feedid)
    {
        $sql = 'SELECT ff.*, fft.title AS type_name, fft.driver FROM ff_feed_field ff, ff_feed_field_type fft WHERE ff.feed_id=? AND fft.id=ff.feed_field_type_id';
        $query = $this->db->query($sql, array($feedid));
        if($query->num_rows() > 0)return $query->result_array();
        return false;
    }
    
    function add_feed_field($feedid, $title, $typeid)
    {
        $feedshort = $this->_get_short($feedid, 'ff_feed');
        $fieldshort = $this->_get_url_title($title);
        
        $this->db->insert('ff_feed_field', array('feed_id'=>$feedid, 'short'=>$fieldshort, 'title'=>$title, 'feed_field_type_id'=>$typeid));
        $this->load->driver('field_type');
        $this->dbforge->add_column($feedshort, array($fieldshort => $this->field_type->get_database_column_type($typeid)));
    }
    
    function update_feed_field($feedid, $fieldid, $title, $typeid)
    {
        $feedshort = $this->_get_short($feedid, 'ff_feed');
        $fieldshort = $this->_get_short($fieldid, 'ff_feed_field');
        $newshort = $this->_get_url_title($title);
        
        $this->db->update('ff_feed_field', array('title'=>$title, 'short'=>$newshort, 'feed_field_type_id'=>$typeid), array('id'=>$fieldid));
        $this->load->driver('field_type');
        $fielddata = array_merge($this->field_type->get_database_column_type($typeid), array('name'=>$newshort));
        $this->dbforge->modify_column($feedshort, array($fieldshort=>$fielddata));
    }
    
    function delete_feed_field($feedid, $fieldid)
    {
        $feedshort = $this->_get_short($feedid, 'ff_feed');
        $fieldshort = $this->_get_short($fieldid, 'ff_feed_field');
        
        $this->db->delete('ff_feed_field', array('id' => $fieldid));
        $this->dbforge->drop_column($feedshort, $fieldshort);
    }
    
    function add_feed_entry($feedid, $entrydata)
    {
        $feedshort = $this->_get_short($feedid, 'ff_feed');
        $this->db->insert($feedshort, $entrydata);
    }
    
    function update_feed_entry($feedid, $entryid, $entrydata)
    {
        $feedshort = $this->_get_short($feedid, 'ff_feed');
        $this->db->update($feedshort, $entrydata, array('id'=>$entryid));
    }
    
    function delete_feed_entry($feedid, $entryid)
    {
        $feedshort = $this->_get_short($feedid, 'ff_feed');
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
        $sql = 'SELECT ff.short, fft.driver FROM ff_feed f, ff_feed_field ff, ff_feed_field_type fft WHERE f.short=? AND ff.feed_id=f.id AND fft.id=ff.feed_field_type_id';
        $query = $this->db->query($sql, array($feedshort));
        
        if($query->num_rows() > 0)
        {
            $types = array();
            $results = $query->result_array();
            foreach($results as $result)
                $types[$result['short']] = $result['driver'];
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
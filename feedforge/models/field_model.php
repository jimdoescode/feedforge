<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Field_model extends CI_Model
{
    function get_field_types($justdrivers = false)
    {
        $query = $this->db->get('ff_feed_field_type');
        if($query->num_rows() > 0)
        {
            if(!$justdrivers)return $query->result_array();
            else
            {
                $drivers = array();
                foreach($query->result_array() as $row)
                    array_push($drivers, "field_type_{$row['driver']}");
                return $drivers;
            }
        }
        return false;
    }

    public function get_field_driver($typeid)
    {
        $query = $this->db->select('driver')->where('id', $typeid)->get('ff_feed_field_type');
        if($query->num_rows() > 0)return $query->row()->driver;
        return false;
    }
}
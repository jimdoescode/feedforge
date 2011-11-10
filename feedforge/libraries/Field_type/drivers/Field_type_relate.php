<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Field_type_relate extends CI_Driver
{
    public function get_database_column_type()
    {
        //Same size as the short value of a feed title
        return array('type'=>'VARCHAR','constraint'=>32);
    }

    public function database_preprocess($value)
    {
       return $value;
    }

    public function display_admin_input($name)
    {
        $ci =& get_instance();
        $ci->load->model('feed_model');
        $html = "<select name='{$name}' id='{$name}'>";
        $feeds = $ci->feed_model->get_feeds();
        foreach($feeds as $feed)
            $html .= "<option value='{$feed['short']}'>{$feed['title']}</option>";

        $html .= "</select>";
        return $html;
    }

    public function display_tag_value($value, $params = array())
    {
        $output = '';
        if(array_key_exists('internal', $params))
        {
            $ci =& get_instance();
            $ci->load->model('feed_model');
            $entries = $ci->feed_model->get_template_feed_entries($value);

            if($entries !== false)
            {
                $count = count($entries);
                for($i=0; $i < $count; ++$i)
                {
                    $internal = $params['internal'];
                    foreach($entries[$i] as $key => $entry)
                        $internal = str_replace('{'.$key.'/}', $entry, $internal);
                    $output .= $internal;
                }
            }
        }
        return $output;
    }
}


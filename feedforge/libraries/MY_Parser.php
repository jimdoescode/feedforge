<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Parser extends CI_Parser
{
    private $keys = 'id';
    private $template_directory = 'templates/';
    
    function __construct($params)
    {
        $this->ci = get_instance();
        $this->ci->load->model('feed_model');
    }
    
    function parse_template($path, $globals = array(), $merged = false)
    {
        //Get the initial file
        $template = $this->ci->load->file($this->template_directory.$path.EXT, true);
        //Fill in any global variables from a merge call
        if($merged)$template = $this->_parse_globals($template, $globals, 'merge:');
        //Fill in any templates that are merged into this one
        $template = $this->_parse_merges($template);
        //Fill in any standard global variables.
        $template = $this->_parse_globals($template, $globals);
        //Fill in any feed data
        $template = $this->_parse_feeds($template);
        
        return $template;
    }
    
    private function _parse_globals($template, $globals, $pre = 'global:')
    {
        $globalArray = array();
        foreach($globals as $key => $val)
            $globalArray[$pre.$key] = $val;
        
        $template = $this->parse_string($template, $globalArray, true);
        return $template;
    }
    
    private function _parse_merges($template)
    {
        $reg = "/\\{$this->l_delim}ff:merge\s*=\s*\"(.+?)\"\s*(.*?)\\{$this->r_delim}/s";
        preg_match_all($reg, $template, $mergedata);
        $mergecount = count($mergedata[0]);
        for($i=0; $i < $mergecount; $i++)
        {
            $params = $this->_get_params($mergedata[2][$i]);
            $subtemplate = $this->parse_template($mergedata[1][$i], $params, true);
            $template = str_replace($mergedata[0][$i], $subtemplate, $template);
        }
        return $template;
    }
    
    private function _parse_feeds($template)
    {
        $reg = "/\\{$this->l_delim}ff:feed\s*=\s*\"(.+?)\"\s*(.*?)\\{$this->r_delim}(.+?)\\{$this->l_delim}\/ff:feed\\{$this->r_delim}/s";
        preg_match_all($reg, $template, $feeddata);
        $feedcount = count($feeddata[0]);
        for($i=0; $i < $feedcount; $i++)
        {
            $params = $this->_get_params($feeddata[2][$i]);
            $tags = $this->_get_tags($feeddata[3][$i]);
            $subtemplate = $this->_parse_feed_tags($feeddata[1][$i], $params, $tags, $feeddata[3][$i]);
            $template = str_replace($feeddata[0][$i], $subtemplate, $template);
        }
        return $template;
    }
    
    private function _parse_feed_tags($feed, $feedparams, $tagdata, $segment)
    {
        $types = $this->ci->feed_model->get_feed_entry_types($feed);
        $entries = $this->ci->feed_model->get_feed_entries($feed);
        $template = '';
        if($entries !== false)
        {
            $count = count($entries);
            error_log(print_r($entries, true));
            error_log(print_r($tagdata, true));
            error_log(print_r($types, true));
            for($i=0; $i < $count; $i++)
            {
                $template .= $segment;
                foreach($entries[$i] as $key => $val)
                {
                    //If we don't have any tag data then ignore this tag.
                    if(!array_key_exists($key, $tagdata))continue;
                    //If we have a type then we will display the value from it.
                    if(array_key_exists($key, $types))
                    {
                        $this->ci->load->library('field_types/'.$types[$key], null, 'format');
                        if(is_array($tagdata[$key]))$val = $this->ci->format->display($val, $tagdata[$key][1]);
                        else $val = $this->ci->format->display($val);
                    }
                    $template = str_replace($tagdata[$key], $val, $template);
                }
            }
        }
        return $template;
    }
    
    private function _get_params($internal)
    {
        $paramarray = array();
        preg_match_all("/([\w]*?)\s*=\s*\"(.*?)\"/s", $internal, $params);
        $paramarray = false;
        if(count($params) > 0)$paramarray = array_combine($params[1], $params[2]);
        return $paramarray;
    }
    
    private function _get_tags($internal)
    {
        $tagarray = array();
        preg_match_all("/\\{$this->l_delim}\s*([a-zA-Z0-9_\-]+)\s*(.*?)\\{$this->r_delim}/s", $internal, $tags);
        $tagcount = count($tags[0]);
        for($i=0; $i < $tagcount; $i++)
        {
            if(strlen($tags[2][$i]) > 0)
            {
                $params = $this->_get_params($tags[2][$i]);
                $tagarray[$tags[1][$i]] = array($tags[0][$i], $params);//TODO: This wont work in cases of multiple of the same tag with different params
            }
            else $tagarray[$tags[1][$i]] = $tags[0][$i];
        }
        return $tagarray;
    }
}

?>
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class FF_Parser extends CI_Parser
{
    private $quote = '"';
    
    function __construct()
    {
        $this->ci = get_instance();
        $this->ci->load->model('feed_model');
        $this->ci->load->driver('field_type');
    }
    
    function parse_template($path, $globals = false, $merged = false)
    {
        //Build the full path to the template and make sure it exists.
        $fullpath = TEMPPATH.$path.EXT;
        if(!file_exists($fullpath))return false;
        if($globals === false)$globals = array();
        //Get the initial file.
        $template = $this->ci->load->file($fullpath, true);
        //Fill in any global variables from a merge call
        if($merged)$template = $this->_parse_globals($template, $globals, 'merge:var');
        //Fill in any templates that are merged into this one
        $template = $this->_parse_merges($template);
        //Fill in any standard global variables.
        $template = $this->_parse_globals($template, $globals);
        //Fill in any feed data
        $template = $this->_parse_feeds($template);
        
        return $template;
    }
    
    private function _parse_globals($template, $globals, $pre = 'ff:global')
    {
        $globalArray = array();
        foreach($globals as $key => $val)
            $globalArray[$pre.'='.$this->quote.$key.$this->quote] = $val;
        
        $template = $this->parse_string($template, $globalArray, true);
        return $template;
    }
    
    private function _parse_merges($template)
    {
        $reg = "/\\{$this->l_delim}ff:merge\s*=\s*{$this->quote}(.+?){$this->quote}\s*(.*?)\\{$this->r_delim}/s";
        preg_match_all($reg, $template, $mergedata);
        $mergecount = count($mergedata[0]);
        for($i=0; $i < $mergecount; ++$i)
        {
            $params = $this->_get_params($mergedata[2][$i]);
            $subtemplate = $this->parse_template($mergedata[1][$i], $params, true);
            if($subtemplate !== false)$template = str_replace($mergedata[0][$i], $subtemplate, $template);
        }
        return $template;
    }
    
    private function _parse_feeds($template)
    {
        $reg = "/\\{$this->l_delim}ff:feed\s*=\s*{$this->quote}(.+?){$this->quote}\s*(.*?)\\{$this->r_delim}(.+?)\\{$this->l_delim}\/ff:feed\\{$this->r_delim}/s";
        preg_match_all($reg, $template, $feeddata);
        $feedcount = count($feeddata[0]);
        for($i=0; $i < $feedcount; ++$i)
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
        $template = '';
        if($types !== false)
        {
            $entries = false;
            if(is_array($feedparams))
            {
                $count = array_key_exists('count', $feedparams) ? $feedparams['count'] : 30;
                $start = array_key_exists('start', $feedparams) ? $feedparams['start'] : 0;
                $feedparams = array_diff_key($feedparams, array('count'=>false, 'start'=>false));//It really doesn't matter what the values of the diff array are.
                
                $entries = $this->ci->feed_model->get_template_feed_entries($feed, $feedparams, $count, $start);
            }
            else $entries = $this->ci->feed_model->get_template_feed_entries($feed);
            if($entries !== false)
            {
                $count = count($entries);
                for($i=0; $i < $count; ++$i)
                {
                    $template .= $segment;
                    foreach($entries[$i] as $key => $val)
                    {
                        //If we don't have any tag data then ignore this tag.
                        if(!array_key_exists($key, $tagdata))continue;
                        //If we have a type then we will display the value from it.
                        if(is_array($tagdata[$key]))
                        {
                            $tagscount = count($tagdata[$key]);
                            for($j=0; $j < $tagscount; ++$j)
                            {
                                if(array_key_exists($key, $types))$val = $this->ci->field_type->{$types[$key]}->display_tag_value($val, $tagdata[$key][$j][1]);
                                $template = str_replace($tagdata[$key][$j][0], $val, $template);
                            }
                        }
                        else
                        {
                            if(array_key_exists($key, $types))$val = $this->ci->field_type->{$types[$key]}->display_tag_value($val, false);
                            $template = str_replace($tagdata[$key], $val, $template);
                        }
                    }
                }
            }
        }
        return $template;
    }
    /**
     * Parses a string of parameters in the form x="y" a="b"
     * into an associative array with keys being the variable
     * and values being the values (without quotes).
     **/
    private function _get_params($internal)
    {
        preg_match_all("/([\w]*?)\s*=\s*{$this->quote}(.*?){$this->quote}/s", $internal, $params);
        
        $paramarray = false;
        if(count($params[0]) > 0)$paramarray = array_combine($params[1], $params[2]);
        return $paramarray;
    }
    
    private function _get_tags($internal)
    {
        $tagarray = array();
        preg_match_all("/\\{$this->l_delim}\s*([a-zA-Z0-9_\-]+)\s*(.*?)\\{$this->r_delim}/s", $internal, $tags);
        $tagcount = count($tags[0]);
        for($i=0; $i < $tagcount; ++$i)
        {
            if(strlen($tags[2][$i]) > 0)
            {
                $params = $this->_get_params($tags[2][$i]);
                if(array_key_exists($tags[1][$i], $tagarray))array_push($tagarray[$tags[1][$i]], array($tags[0][$i], $params));
                else $tagarray[$tags[1][$i]] = array(array($tags[0][$i], $params));
            }
            else $tagarray[$tags[1][$i]] = $tags[0][$i];
        }
        return $tagarray;
    }
}

?>
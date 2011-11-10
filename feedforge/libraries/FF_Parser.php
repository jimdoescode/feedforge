<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class FF_Parser extends CI_Parser
{
    private $quote = '"';

    function __construct()
    {
        $this->ci =& get_instance();
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

        $this->set_delimiters('{', '/}');
        $template = $this->parse_string($template, $globalArray, true);
        $this->set_delimiters(); //Reset delimiters
        return $template;
    }
    
    private function _parse_merges($template)
    {
        $reg = "/\\{$this->l_delim}ff:merge\s*=\s*{$this->quote}(.+?){$this->quote}\s*(.*?)\\/\\{$this->r_delim}/s";
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
            $subtemplate = $this->_parse_feed_tags($feeddata[1][$i], $params, $this->_get_single_tags($feeddata[3][$i]), $this->_get_tag_pairs($feeddata[3][$i]), $feeddata[3][$i]);
            $template = str_replace($feeddata[0][$i], $subtemplate, $template);
        }
        return $template;
    }
    
    private function _parse_feed_tags($feed, $feedparams, $singletagdata, $tagpairdata, $segment)
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
                        if(!isset($val))continue;
                        //Does the key exist as a single tag?
                        if(array_key_exists($key, $singletagdata))
                        {
                            //Does the tag have parameters?
                            if(is_array($singletagdata[$key]))
                            {
                                $tagscount = count($singletagdata[$key]);
                                for($j=0; $j < $tagscount; ++$j)
                                {
                                    $replacement = $val;
                                    if(array_key_exists($key, $types))$replacement = $this->ci->field_type->{$types[$key]}->display_tag_value($val, $singletagdata[$key][$j][1]);
                                    $template = str_replace($singletagdata[$key][$j][0], $replacement, $template);
                                }
                            }
                            else
                            {
                                $replacement = $val;
                                if(array_key_exists($key, $types))$replacement = $this->ci->field_type->{$types[$key]}->display_tag_value($val, false);
                                $template = str_replace($singletagdata[$key], $replacement, $template);
                            }
                        }
                        //Does the key exist as a tag pair?
                        elseif(array_key_exists($key, $tagpairdata))
                        {
                            $tagscount = count($tagpairdata[$key]);
                            for($j=0; $j < $tagscount; ++$j)
                            {
                                $replacement = $val;
                                if(array_key_exists($key, $types))$replacement = $this->ci->field_type->{$types[$key]}->display_tag_value($val, $tagpairdata[$key][$j][1]);

                                $template = str_replace($tagpairdata[$key][$j][2], $replacement, $template);
                            }
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

    /**
     * Parses $internal in search of single tags {xxxxxx/} then returns them as an array
     * @param $internal The string to check for single tags
     * @return array The located tag data
     */
    private function _get_single_tags($internal)
    {
        $tagarray = array();
        preg_match_all("/\\{$this->l_delim}\s*([a-zA-Z0-9_\-]+)\s*([\w]*\s*=\s*{$this->quote}.*{$this->quote})*\\/\\{$this->r_delim}/s", $internal, $tags);
        $tagcount = count($tags[0]);
        for($i=0; $i < $tagcount; ++$i)
        {
            //Do we have parameters that we need to add?
            if(strlen($tags[2][$i]) > 0)
            {
                $params = $this->_get_params($tags[2][$i]);
                if(array_key_exists($tags[1][$i], $tagarray))array_push($tagarray[$tags[1][$i]], array($tags[0][$i], $params));
                else $tagarray[$tags[1][$i]] = array(array($tags[0][$i], $params));
            }
            elseif(array_key_exists($tags[1][$i], $tagarray) && is_array($tagarray[$tags[1][$i]]))array_push($tagarray[$tags[1][$i]], array($tags[0][$i], array()));
            else $tagarray[$tags[1][$i]] = $tags[0][$i];
        }
        return $tagarray;
    }

    /**
     * Parses internal in search of tag pairs {xxxx}yyyyy{/xxxx} then returns them as an array
     * @param $internal The string to check for tags pairs
     * @return array The located tag data
     */
    private function _get_tag_pairs($internal)
    {
        $starttags = array();
        $tagarray = array();
        preg_match_all("/\\{$this->l_delim}\s*([a-zA-Z0-9_\-]+)\s*([a-zA-Z0-9='\"\s]*?)\\{$this->r_delim}/", $internal, $starttags);
        $count = count($starttags[0]);
        for($i=0; $i < $count; ++$i)
        {
            $reg = "/\\{$this->l_delim}{$starttags[1][$i]}\\{$this->r_delim}(.+?)\\{$this->l_delim}\/{$starttags[1][$i]}\\{$this->r_delim}/s";
            //Do we have parameters that we need to add?
            if(strlen($starttags[2][$i]) > 0)
            {
                $params = $this->_get_params($starttags[2][$i]);
                //Is this tag already in our array? If so we need to associate a new array with the key.
                if(array_key_exists($starttags[1][$i], $tagarray))array_push($tagarray[$starttags[1][$i]], array($starttags[0][$i], $params));
                else $tagarray[$starttags[1][$i]] = array(array($starttags[0][$i], $params));
                
                $reg = "/\\{$this->l_delim}{$starttags[1][$i]}\s+{$starttags[2][$i]}\\{$this->r_delim}(.+?)\\{$this->l_delim}\/{$starttags[1][$i]}\\{$this->r_delim}/s";
            }
            elseif(array_key_exists($starttags[1][$i], $tagarray))array_push($tagarray[$starttags[1][$i]], array($starttags[0][$i], array()));
            else $tagarray[$starttags[1][$i]] = array(array($starttags[0][$i], array()));

            preg_match($reg, $internal, $pairdata);
            $tagarray[$starttags[1][$i]][$i][1]['internal'] = $pairdata[1];
            $tagarray[$starttags[1][$i]][$i][2] = $pairdata[0];
        }
        return $tagarray;
    }
}

?>
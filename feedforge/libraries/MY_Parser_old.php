<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Parser extends CI_Parser
{
    private $keys = 'id';
    private $template_path = 'templates/';
    
    function MY_Parser($params)
    {
        if(array_key_exists('keys', $params))$this->keys = implode('|', $params['keys']);
        $this->ci = get_instance();
    }
    
    public function parse_template($template, $params = array())
    {
        $this->ci->load->model('feed_model');
        $template = $this->ci->load->file($this->template_path.$template.EXT, true);
        $template = $this->populate_merge($template);
        $feeds = $this->get_feeds($template);
        foreach($feeds as $feed)
        {
            //The id indicates which feed we are looking at.
            if(array_key_exists('id', $feed['params']))
            {
                $entries = null;
                //Are they specifying a single entry?
                if(array_key_exists('entry', $feed['params']))$entries = $this->ci->feed_model->get_feed_entry($feed['params']['entry']);
                else $entries = $this->ci->feed_model->get_feed_entries($feed['params']['id']);
                $output = '';
                foreach($entries as $entry)
                    $output .= $this->parse_tags($entry, $feed['tags'], $feed['internal']);
                    
                $template = str_replace($feed['full'], $output, $template);  
            }
            else
            {
                $template = false;
                break;
            }
        }
        return $template;
    }
    
    public function get_feeds($template)
    {
        $reg = "/\\{$this->l_delim}ff:feed\s+(.*?)\\{$this->r_delim}(.+?)\\{$this->l_delim}\/ff:feed\\{$this->r_delim}/s";
        preg_match_all($reg, $template, $match);
        
        error_log(print_r($match, true));
        
        $feedcount = count($match[0]);//The number of feed tag pairs in a template
        $feeddata = array();
        for($i=0; $i < $feedcount; $i++)
        {
            $feeddata[$i]['full'] = $match[0][$i];
            $feeddata[$i]['params'] = $this->_get_params($match[1][$i]);
            $feeddata[$i]['internal'] = $match[2][$i];
            $feeddata[$i]['tags'] = $this->_get_tags($match[2][$i]);
        }
        return $feeddata;
    }
    
    public function parse_tags($entry, $tags, $snippet)
    {
        foreach($entry as $key => $value)
        {
            //No tags here... move along.
            if(!array_key_exists($key, $tags))continue;
            $tag = $tags[$key];
            //Is this a custom field?
            if(is_array($value))
            {
                $this->ci->load->library($value['library'], null, 'field');
                $fieldparams = null;
                if(is_array($tags[$key]))
                {
                    $tag = $tags[$key]['tag'];
                    $fieldparams = $tags[$key]['params'];
                }
                $value = $this->ci->field->render_external($value['value'], $fieldparams);
            }
            $snippet = str_replace($tag, $value, $snippet);
        }
        return $snippet;
    }
    
    public function populate_variables($template)
    {
        
    }
    
    public function populate_merge($template)
    {
        $reg = "/\\{$this->l_delim}ff:merge\s*=\s*\"(.+?)\"\s*(.*?)\\{$this->r_delim}/s";
        preg_match_all($reg, $template, $merges);
        
        for($i=0; $i < count($merges[1]); $i++)
        {
            preg_match_all("/\s*([a-z_-]*?)\s*=\s*\"(.*?)\"/s", $merges[2][$i], $matches);
            $params = array();
            foreach($matches[0] as $index => $key)
                $params[$key] = $matches[2][$index];
            
            $template = str_replace($merges[0][$i], $this->ci->load->file($merges[1][$i], true), $template);
        }
        return $template;
    }
    
    private function _get_tags($internal)
    {
        $tagarray = array();
        preg_match_all("/\\{[\w-].*?\\}/s", $internal, $tags);
        foreach($tags[0] as $tag)
        {
            $found = preg_match_all("/([\w\d-]*?)\s*([\w-]*?)\s*=\s*\"(.*?)\"/s", $tag, $tagparams);
            if($found > 0)
            {
                $name = $tagparams[1][0];
                $tagarray[$name]['tag'] = $tag;
                $paramcount = count($tagparams[2]);
                for($i=0; $i < $paramcount; $i++)
                    $tagarray[$name]['params'][$tagparams[2][$i]] = $tagparams[3][$i];
            }
            else $tagarray[substr($tag, 1, strlen($tag)-2)] = $tag;
        }
        
        return $tagarray;
    }
    
    private function _get_params($internal)
    {
        $paramarray = array();
        preg_match_all("/({$this->keys})\s*=\s*\"([\w\d-]*?)\"/s", $internal, $params);
        $paramcount = count($params[0]);
        for($j=0; $j < $paramcount; $j++)
            $paramarray[$params[1][$j]] = $params[2][$j];
        return $paramarray;
    }

    function replace_first($search, $replace, $subject)
    {
        $firstChar = strpos($subject, $search);
        if($firstChar !== false)
        {
            $beforeStr = substr($subject,0,$firstChar);
            $afterStr = substr($subject, $firstChar + strlen($search));
            return $beforeStr.$replace.$afterStr;
        }
        else return $subject;
    }

}

?>
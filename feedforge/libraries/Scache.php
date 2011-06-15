<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This software is open and free you may use it in whatever manner you deem fit. You
 * don't have to give me any credit for it, but it would be nice if you dropped me a
 * line on my blog and let me know how awesome I am.
 *
 * Enjoy!
 *
 * -JimDoesCode
 **/
class Scache
{
    function Scache($params)
    {
        $CI =& get_instance();
        //$CI->load->config('scache'); //config file moved to feedforge config
        $this->expiration = $CI->config->item('cache_expiration');
        //Use the already configured cache path or the default cache path.
        $path = $CI->config->item('cache_path');
        $this->path = ($path == '') ? APPPATH.'cache/' : $path;
    }
    
    /**
     * Remove the specified cache file
     * 
     * @param string $key the identifier for the cache file to be removed.
     **/
    function clear($key)
    {
        $filepath = $this->path.md5($key);
        @unlink($filepath);
    }
    
    /**
     * Checks to see if the specified cache file exists
     * and needs to be updated.
     *
     * @param string $key the identifier for the cache file to check.
     * @param bool $variable flag to determine if we should treat the input as PHP data.
     * @return mixed false if the cache file is out of date or non-existent otherwise the cache file data is returned.
     **/
    function read($key, $variable = false)
    {
        $filepath = $this->path.md5($key);
        if(!@file_exists($filepath))return false;
        if(!$fp = @fopen($filepath, FOPEN_READ))return false;
        
        flock($fp, LOCK_SH);
        $cache = '';
        $size = filesize($filepath);
        if($size > 0)$cache = fread($fp, $size);
        flock($fp, LOCK_UN);
        fclose($fp);
        
        $time = trim(substr($cache, 0, strpos($cache, "\n")));
        $cache = str_replace($time, "", trim($cache));
		
		if($variable !== false)$cache = json_decode($cache, true);
        //We found the file and it has not expired so return it
        if(time() < $time)return $cache;
        //The file has expired delete it
        @unlink($filepath);
        return false;
    }
    
    /**
     * Writes data to a cache file.
     *
     * @param string $key the unique identifier for the cache file.
     * @param string $input the data to cache.
     * @param bool $variable flag to determine if we should treat the input as PHP data.
     **/
    function write($key, $input, $variable = false)
    {
        if(is_dir($this->path) && is_really_writable($this->path))
        {
            $cachepath = $this->path.md5($key);
            if($fp = @fopen($cachepath, FOPEN_WRITE_CREATE_DESTRUCTIVE))
            {
                $expire = time() + ($this->expiration * 60);
                if(flock($fp, LOCK_EX))
                {
					if($variable)$input = json_encode($input);
                    fwrite($fp, $expire."\n".$input);
					flock($fp, LOCK_UN);
                }
                fclose($fp);
				@chmod($cachepath, DIR_WRITE_MODE);
            }
        }
    }
}

?>
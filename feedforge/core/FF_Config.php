<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class FF_Config extends CI_Config
{
    /**
     * Replaces an actual line in the config file specified with the
     * value specified.
     *
     * @param $file the config file this item resides in.
     * @param $item the name of the config item that needs to be updated.
     * @param $value the new value for the config item.
     * @return true on success false otherwise.
     *
     * NOTE: value has to be a string so if you need to encode a config
     * value that is already a string you have to add escaped quotes.
     **/
    function set_item($file, $item, $value)
	{
        foreach($this->_config_paths as $path)
		{
            $check_locations = defined('ENVIRONMENT') ? array(ENVIRONMENT.'/'.$file, $file) : array($file);
			foreach($check_locations as $location)
			{
                $file_path = $path.'config/'.$location.EXT;
				if(file_exists($file_path))
				{
			        if($this->_replace_line($file_path, $item, $value))
                    {
                        $this->config[$item] = $value;
                        return true;
                    }
                    return false;
				}
			}
        }
        return false;
	}
    
    private function _replace_line($path, $item, $value, $config = '$config')
    {
        $lines = file($path);
        $count = count($lines);
        for($i=0; $i < $count; $i++)
        {
            $configline = $config.'[\''.$item.'\']';
            if(strstr($lines[$i], $configline))
            {
                $lines[$i] = $configline.' = '.$value.';';
                $file = implode(PHP_EOL, $lines);
                $handle = @fopen($path, 'w');
                fwrite($handle, $file);
                fclose($handle);
                return true;
            }
        }
        return false;
    }
}

?>
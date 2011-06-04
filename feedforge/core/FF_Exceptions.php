<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class FF_Exceptions extends CI_Exceptions
{
    /**
	 * General Error Page
	 *
	 * This function takes an error message as input
	 * (either as a string or an array) and displays
	 * it using the specified template.
	 *
	 * @access	private
	 * @param	string	the heading
	 * @param	string	the message
	 * @param	string	the template name
	 * @return	string
	 */
	function show_error($heading, $message, $template = 'error_general', $status_code = 500)
	{
		set_status_header($status_code);

		$message = '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>';

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}
		ob_start();
        $path = file_exists(TEMPPATH.'errors/'.$template.EXT) ? TEMPPATH.'errors/'.$template.EXT : APPPATH.'errors/'.$template.EXT;
		include($path);
		$buffer = ob_get_contents();
		ob_end_clean();
        
        $buffer = str_replace(array('{ff:error_heading}', '{ff:error_message}'), array($heading, $message), $buffer);
        
		return $buffer;
	}    
}

?>
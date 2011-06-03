<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Simpauth is meant to make authentication on CodeIgniter well, simple. 
 * Without sacrificing the configurability that makes developing on CI
 * so fun and easy. It comes with one library file one helper file and
 * one config file. All are hopefully well documented (Aside from the
 * occasional misspelling) and if you have any questions feel free to
 * drop me a line.
 *
 * It is recommended that you setup database sessions in codeigniter
 * prior to running this library. Otherwise you might have issues with
 * cookie size and/or security as this library does make heavy use of
 * session data. Visit the link below for more information.
 * http://codeigniter.com/user_guide/libraries/sessions.html
 *
 * This library was designed for and run using a MySQL database and no
 * guarentees can be made that it will play nice with other db types.
 * 
 * Simpauth is distributed under the "vote for me license" which states
 * that you can do whatever you want with this code however you want, 
 * with no warrentees or guarantees. If you like it and you see my name
 * on a presidential ballot somewhere then give me a vote (Gotta start
 * the trip to world domination somewhere).
 *
 * Have fun!
 **/
class Simpauth
{
    //A few arrays we will store locally to avoid
    //building them over and over from the config
    private $dbconfig;
    private $cookie;
    private $dbjoin;
    
    //The name of the session value
    private $session_key;
    
    function Simpauth()
    {
        //Setup the CI stuff we need
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->library('session');
        $this->CI->load->library('encrypt');
        $this->CI->load->helper('cookie');
        
        $this->CI->load->config('simpauth');
        //Setup our config arrays.
        $this->dbconfig = $this->CI->config->item('authdb');
        $this->cookie   = $this->CI->config->item('authcookie');
        $this->dbjoin   = $this->CI->config->item('join');
        $key = $this->CI->config->item('simpauth_key');
        $this->session_key = $key == false ? 'auth' : $key;
        //Check login when initialized which
        //rebuild a session from a cookie.
        $this->check_login();
    }
    
    /**
     * Execute login by looking for the db entry that
     * corresponds to the specified condition array.
     * NOTE: It is your responsibility to correctly
     * hash any password data.
     *
     * @param $condition_array the array of login conditions
     * @return true on success false otherwise.
     **/
    function execute_login($condition_array)
    {
        $query = $this->get_all_auth_data($condition_array);
        
        if($query->num_rows() > 0)
        {
            $table = $query->row_array();    
            $this->CI->session->set_userdata($this->session_key, $table);
            if(isset($this->cookie['enable']) && $this->cookie['enable'])
                $this->set_auth_cookie($table[$this->dbconfig['primary']]);
            return true;
        }
        else return false;
    }
    
    private function get_all_auth_data($where)
    {
        $main = $this->dbconfig['table'];
        $this->CI->db->select('*')->from($main);
    
        if($this->dbjoin)
        {
            foreach($this->dbjoin['table'] as $table)
                $this->CI->db->join($table, ($table.'.'.$this->dbjoin['index'].' = '.$main.'.'.$this->dbconfig['primary']), 'left');
        }
        //Build the where clause 
        foreach($where as $key => $value)
        {
            $this->CI->db->where($key, $value);
            //If set in the config then add the or clause
            if(is_array($this->dbconfig['or_check']) && isset($this->dbconfig['or_check'][$key]))
                $this->CI->db->or_where($this->dbconfig['or_check'][$key], $value);
        }
        $this->CI->db->limit(1);
        return $this->CI->db->get();
    }
    
    /**
     * Checks to see if the user has valid auth data
     * stored in the session.
     *
     * @return true if either data exists in the session or the
     * cookie false otherwise.
     **/
    function check_login()
    {
        $auth = $this->CI->session->userdata($this->session_key);
        if($auth === false || !is_array($auth))
        {
            if(!is_array($auth))$this->CI->session->unset_userdata($this->session_key);
            
            if(isset($this->cookie['enable']) && $this->cookie['enable'])return $this->check_auth_cookie();
            else return false;
        }
        else return true;
    }
    
    /**
     * Checks to see if the user is logged in, if not
     * redirects the page to the specified redirectURL
     * if redirectURL is not set the page will be
     * redirected to the base url.
     *
     * @param $redirectURL the url to redirect to, if not set then use the base_url
     **/
    function require_login($redirectURL = false)
    {
        if($this->check_login() === false)
        {
            if($redirectURL === false)$redirectURL = site_url();
            redirect($redirectURL);
        }
    }
    
    /**
     * Complete opposite of require_login. Used if you
     * don't want logged in users visiting certain pages
     * like registration or login pages.
     *
     * @param $redirectURL the url to redirect to, if not set then use the base_url
     **/
    function require_no_login($redirectURL = false)
    {
        if($this->check_login() === true)
        {
            if($redirectURL === false)$redirectURL = site_url();
            redirect($redirectURL);
        }
    }
    
    /**
     * Logout by unsetting all associated session data
     * if cookies are also being used then set the
     * clear_cookie flag to also delete the cookie.
     *
     * @param $clear_cookie optional flag that deletes the auth cookie
     **/
    function execute_logout()
    {
        //Write all session data back to the db
        $this->dump_session();
        $this->CI->session->unset_userdata($this->session_key);
        if($this->cookie['enable'] === true)
        {
            $this->CI->load->helper('cookie');
            delete_cookie($this->cookie['name']);
        }
    }
    
    /**
     * Register a new user with the following values. If you
     * want the user logged in after registration then set
     * the auto_login flag to true.
     * 
     * NOTE: Values should only correspond to your main users 
     * table as specified in the config. If you wish to set
     * data on another table then call the set_value method
     * after this method is called. Also it is your
     * responsibility to hash any password data.  
     *
     * @param $condition_array array of values to insert into your main users table
     * @param $auto_login flag to log the user in prior to registration.
     **/
    function execute_register($condition_array, $auto_login = false)
    {
        //Insert the new registration data into the main users table
        $this->CI->db->insert($this->dbconfig['table'], $condition_array);
        //If there are other tables that join to the main table we will create those
        if($this->dbjoin)
        {
            //Get the Primary key from the newly created user
            $this->CI->db->select($this->dbconfig['primary'])
                         ->from($this->dbconfig['table'])
                         ->where($condition_array);
                         
            $query = $this->CI->db->get();
            $result = $query->row_array();
            //Insert new rows into the join tables setting the appropriate join index
            foreach($this->dbjoin['table'] as $table)
                $this->CI->db->insert($table, array($this->dbjoin['index'] => $result[$this->dbconfig['primary']]));
        }
        
        if($auto_login === true)$this->execute_login($condition_array);
    }
    
    /**
     * Retrieves the value that corresponds to the specified
     * key from the session and returns it. 
     * NOTE: Data is cached in the users session, if the
     * database is updated without rebuilding the session
     * then this method may pull stale data.
     * 
     * @param $key the sql column for the stored value.
     * @param $rebuild write database values to the session.
     * @return the corresponding value or false.
     **/
    function get_value($key, $rebuild = false)
    {
        if($this->check_login() !== false)
        {
            if($rebuild)$this->rebuild_session();
            $auth = $this->CI->session->userdata($this->session_key);
            if(is_array($auth) && isset($auth[$key]))return $auth[$key];
        }
        return false;
    }
    
    /**
     * Sets values to the stored session if logged in
     * setting the dump flag will dump session values
     * out to the database.
     *
     * @param $key the sql column name
     * @param $value the value to put in to column
     * @param $dump write session values to the database
     **/
    function set_value($key, $value, $dump = false)
    {
        $login = $this->CI->session->userdata($this->session_key);
        if($login !== false)
        {
            $login[$key] = $value;
            $this->CI->session->set_userdata($this->session_key, $login);
            if($dump)$this->dump_session();
        }
    }
    
    /**
     * Rebuilds the users session data to whats in the db.
     * This is useful if you have changed a db value and want
     * those changes to be visible immediately otherwise
     * sessions will not be updated until the user logs out
     * then back in.
     **/
    function rebuild_session()
    {
        if($this->check_login() !== false)
        {
            $query = $this->get_all_auth_data(array($this->dbconfig['primary'] => $this->get_value($this->dbconfig['primary'])));
            if($query->num_rows() > 0)
            {
                $table = $query->row_array();
                $this->CI->session->set_userdata($this->session_key, $table);
            }
        }
    }
    
    /**
     * Writes session data out to the various configured
     * db tables.
     * NOTE: It does not update the primary or index
     * columns that are specified in the config.
     **/
    function dump_session()
    {
        $login = $this->CI->session->userdata($this->session_key);
        if($login !== false)
        {
            $tables = $this->get_tables_array();
            foreach($login as $key => $value)
            {
                if($this->valid_key($key))
                {
                    if($tables[$key] === $this->dbconfig['table'])$this->CI->db->where($this->dbconfig['primary'], $this->get_value($this->dbconfig['primary']));
                    elseif(is_array($this->dbjoin)) $this->CI->db->where($this->dbjoin['index'], $this->get_value($this->dbconfig['primary']));
                    $this->CI->db->update($tables[$key], array($key => $value));
                }
            }
        }
    }
    
    /**
     * We don't want to update any primary or index keys
     *
     * @param $key the key to check
     * @return false if this is a primary key or index, true otherwise
     **/
    private function valid_key($key)
    {
        return ($key !== $this->dbconfig['primary'] && $key !== $this->dbjoin['index']);
    }
    
    /**
     * Builds a special array to associate a key or column name
     * to a particular table so we can update the proper table.
     * NOTE: This method may not work as intended if tables have
     * columns with the same names.
     *
     * @return an array of the form $table = array[$column];
     **/
    private function get_tables_array()
    {
        $sqltables = array();
        $table = $this->dbconfig['table'];
        $query = $this->CI->db->query("SHOW COLUMNS FROM ".$table);
        foreach($query->result() as $result)
            if($this->valid_key($result->Field))$sqltables[$result->Field] = $table;
        
        if(is_array($this->dbjoin))
        {
            foreach($this->dbjoin['table'] as $table)
            {
                $query = $this->CI->db->query("SHOW COLUMNS FROM ".$table);
                foreach($query->result() as $result)
                    if($this->valid_key($result->Field))$sqltables[$result->Field] = $table;    
            }
        }
        return $sqltables;
    }
    
    /**
     * Set a cookie with the encrypted info to login
     *
     * @param $id the user id
     **/
    private function set_auth_cookie($id)
    {
        $data = array($id, time(), $this->cookie['salt']);         
        $encrypted = $this->CI->encrypt->encode(serialize($data));
        
        $cookie = array('name'=>$this->cookie['name'],
                        'value'=>$encrypted,
                        'expire'=>'604800');
        
        set_cookie($cookie);
    }
    
    /**
     * Check the cookie to see if we can login using it.
     **/
    private function check_auth_cookie()
    {
        $cookie = get_cookie($this->cookie['name'], true);
        if($cookie !== false)
        {
            $decrypted = unserialize($this->CI->encrypt->decode($cookie));
            if($decrypted[2] == $this->cookie['salt'] && is_numeric($decrypted[0]))
                return $this->execute_login(array($this->dbconfig['primary'] => $decrypted[0]));
        }
        return false;
    }
}
/* Location: ./system/application/libraries/Simpauth.php */
?>
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Install_model extends CI_Model
{
    function create_database($user, $password, $host, $database, $type='mysql')
    {
        $connection = "{$type}://{$user}:{$password}@{$host}/{$database}";
        $this->load->database($connection, false, true);
        $this->load->dbforge();

        //Create and populate the feed table
        $this->dbforge->add_field(array(
            'id'=>array('type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true),
            'short'=>array('type'=>'VARCHAR','constraint'=>32),
            'title'=>array('type'=>'VARCHAR','constraint'=>32)
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('short');
        $this->dbforge->create_table('ff_feed', true);
        $feeds = array(
            array('short'=>'demo-feed', 'title'=>'Demo Feed'),
            array('short'=>'related-feed', 'title'=>'Related Feed')
        );
        $this->db->insert_batch('ff_feed', $feeds);

        //Create and populate the feed field table
        $this->dbforge->add_field(array(
            'id'=>array('type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true),
            'feed_id'=>array('type'=>'INT','constraint'=>11),
            'short'=>array('type'=>'VARCHAR','constraint'=>32),
            'title'=>array('type'=>'VARCHAR','constraint'=>32),
            'feed_field_type_id'=>array('type'=>'INT','constraint'=>11)
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('short');
        $this->dbforge->create_table('ff_feed_field', true);
        $fields = array(
            array('feed_id'=>1, 'short'=>'welcome-text', 'title'=>'Welcome Text', 'feed_field_type_id'=>1),
            array('feed_id'=>1, 'short'=>'welcome-message', 'title'=>'Welcome Message', 'feed_field_type_id'=>2),
            array('feed_id'=>1, 'short'=>'cinco-de-mayo', 'title'=>'Cinco De Mayo', 'feed_field_type_id'=>3),
            array('feed_id'=>2, 'short'=>'test-related', 'title'=>'Test Related', 'feed_field_type_id'=>1),
            array('feed_id'=>1, 'short'=>'relation', 'title'=>'Relation', 'feed_field_type_id'=>4),
        );
        $this->db->insert_batch('ff_feed_field', $fields);

        //Create and populate the feed field type table
        $this->dbforge->add_field(array(
            'id'=>array('type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true),
            'driver'=>array('type'=>'VARCHAR','constraint'=>32),
            'title'=>array('type'=>'VARCHAR','constraint'=>32)
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('driver');
        $this->dbforge->add_key('title');
        $this->dbforge->create_table('ff_feed_field_type', true);
        $types = array(
            array('driver'=>'small_text', 'title'=>'Text Input Field'),
            array('driver'=>'large_text', 'title'=>'Text Area Field'),
            array('driver'=>'date', 'title'=>'Date Input Field'),
            array('driver'=>'relate', 'title'=>'Relate Feed Field')
        );
        $this->db->insert_batch('ff_feed_field_type', $types);

        //Create session table
        $this->dbforge->add_field(array(
            'session_id'=>array('type'=>'VARCHAR','constraint'=>40,'default'=>'0'),
            'ip_address'=>array('type'=>'VARCHAR','constraint'=>16,'default'=>'0'),
            'user_agent'=>array('type'=>'VARCHAR','constraint'=>120),
            'last_activity'=>array('type'=>'INT','constraint'=>10,'unsigned'=>true,'default'=>0),
            'user_data'=>array('type'=>'TEXT')
        ));
        $this->dbforge->add_key('session_id', true);
        $this->dbforge->create_table('ff_session', true);

        //Create and populate the variable table
        $this->dbforge->add_field(array(
            'id'=>array('type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true),
            'short'=>array('type'=>'VARCHAR','constraint'=>64),
            'title'=>array('type'=>'VARCHAR','constraint'=>64),
            'value'=>array('type'=>'VARCHAR','constraint'=>128)
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('short');
        $this->dbforge->create_table('ff_variable', true);
        $vars = array(
            array('short'=>'css', 'title'=>'CSS', 'value'=>'assets/css/style.css')
        );
        $this->db->insert_batch('ff_variable', $vars);

        //Create and populate the demo feed
        $this->dbforge->add_field(array(
            'id'=>array('type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true),
            'welcome-text'=>array('type'=>'VARCHAR','constraint'=>128,'null'=>true),
            'welcome-message'=>array('type'=>'TEXT','null'=>true),
            'cinco-de-mayo'=>array('type'=>'DATE','null'=>true),
            'relation'=>array('type'=>'VARCHAR','constraint'=>32)
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('demo-feed', true);
        $demo = array(
            array('welcome-text'=>'Welcome to Feed Forge',
                  'welcome-message'=>'This message is being generated via a feed called \'demo-feed\' and a single entry. You can see how the template uses feed tags by going to the templates directory located in the root directory. You can also add or update feeds by going to the <a href="'.site_url('admin').'">admin</a> screen.',
                  'cinco-de-mayo'=>'2011-05-05',
                  'relation'=>'related-feed'
            )
        );
        $this->db->insert_batch('demo-feed', $demo);

        //Create and populate the related feed
        $this->dbforge->add_field(array(
            'id'=>array('type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true),
            'test-related'=>array('type'=>'VARCHAR', 'constraint'=>128, 'null'=>true)
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('related-feed', true);
        $related = array(
            array('test-related'=>'This is the value of a related field'),
            array('test-related'=>'Another value of a related field')
        );
        $this->db->insert_batch('related-feed', $related);

        return true;
    }
}

?>
-- Feed Forge Installation SQL Dump
--

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `feedforge`
--

-- --------------------------------------------------------

--
-- Table structure for table `demo-feed`
--

CREATE TABLE IF NOT EXISTS `demo-feed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `welcome-text` varchar(128) DEFAULT NULL,
  `welcome-message` varchar(1024) DEFAULT NULL,
  `cinco-de-mayo` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `demo-feed`
--

INSERT INTO `demo-feed` VALUES(1, 'Welcome to Feed Forge', 'This message is being generated via a feed called "demo-feed" and a single entry. You can see how the template uses feed tags by going to the templates directory located in the root directory. You can also add or update feeds by going to the admin screen.', '1984-05-05');

-- --------------------------------------------------------

--
-- Table structure for table `ff_feed`
--

CREATE TABLE IF NOT EXISTS `ff_feed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `short` varchar(32) NOT NULL,
  `title` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `short` (`short`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `ff_feed`
--

INSERT INTO `ff_feed` VALUES(1, 'demo-feed', 'Demo Feed');

-- --------------------------------------------------------

--
-- Table structure for table `ff_feed_field`
--

CREATE TABLE IF NOT EXISTS `ff_feed_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_id` int(11) NOT NULL,
  `short` varchar(32) NOT NULL,
  `title` varchar(32) NOT NULL,
  `feed_field_type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `short` (`short`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `ff_feed_field`
--

INSERT INTO `ff_feed_field` VALUES(1, 1, 'welcome-text', 'Welcome Text', 1);
INSERT INTO `ff_feed_field` VALUES(2, 1, 'welcome-message', 'Welcome Message', 2);
INSERT INTO `ff_feed_field` VALUES(4, 1, 'cinco-de-mayo', 'Cinco De Mayo', 3);

-- --------------------------------------------------------

--
-- Table structure for table `ff_feed_field_type`
--

CREATE TABLE IF NOT EXISTS `ff_feed_field_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `library` varchar(32) NOT NULL,
  `title` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `library` (`library`),
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `ff_feed_field_type`
--

INSERT INTO `ff_feed_field_type` VALUES(1, 'small_text', 'Text Input Field');
INSERT INTO `ff_feed_field_type` VALUES(2, 'large_text', 'Text Area Field');
INSERT INTO `ff_feed_field_type` VALUES(3, 'date', 'Date Input Field');

-- --------------------------------------------------------

--
-- Table structure for table `ff_variable`
--

CREATE TABLE IF NOT EXISTS `ff_variable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `short` varchar(64) NOT NULL,
  `value` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `short` (`short`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ff_variable`
--

INSERT INTO `ff_variable` VALUES(1, 'CSS', 'css', 'assets/css/style.css');

-- --------------------------------------------------------

--
-- Table structure for table `ff_session`
--

CREATE TABLE IF NOT EXISTS  `ff_session` (
  `session_id` varchar(40) DEFAULT '0' NOT NULL,
  `ip_address` varchar(16) DEFAULT '0' NOT NULL,
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned DEFAULT 0 NOT NULL,
  `user_data` text DEFAULT '' NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
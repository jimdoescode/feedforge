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

INSERT INTO `demo-feed` VALUES(1, 'Welcome to FeedForge', 'This message is being generated via a feed called "demo-feed" and a single entry. You can see how the template uses feed tags by going to the templates directory located in the root directory. You can also add or update feeds by going to the admin screen.', '1984-05-05');

-- --------------------------------------------------------

--
-- Table structure for table `feed`
--

CREATE TABLE IF NOT EXISTS `feed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `short` varchar(32) NOT NULL,
  `title` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `short` (`short`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `feed`
--

INSERT INTO `feed` VALUES(1, 'demo-feed', 'Demo Feed');

-- --------------------------------------------------------

--
-- Table structure for table `feed_field`
--

CREATE TABLE IF NOT EXISTS `feed_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_id` int(11) NOT NULL,
  `short` varchar(32) NOT NULL,
  `title` varchar(32) NOT NULL,
  `feed_field_type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `short` (`short`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `feed_field`
--

INSERT INTO `feed_field` VALUES(1, 1, 'welcome-text', 'Welcome Text', 1);
INSERT INTO `feed_field` VALUES(2, 1, 'welcome-message', 'Welcome Message', 2);
INSERT INTO `feed_field` VALUES(4, 1, 'cinco-de-mayo', 'Cinco De Mayo', 3);

-- --------------------------------------------------------

--
-- Table structure for table `feed_field_type`
--

CREATE TABLE IF NOT EXISTS `feed_field_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `library` varchar(32) NOT NULL,
  `title` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `library` (`library`),
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `feed_field_type`
--

INSERT INTO `feed_field_type` VALUES(1, 'small_text', 'Text Input Field');
INSERT INTO `feed_field_type` VALUES(2, 'large_text', 'Text Area Field');
INSERT INTO `feed_field_type` VALUES(3, 'date', 'Date Input Field');

-- --------------------------------------------------------

--
-- Table structure for table `variable`
--

CREATE TABLE IF NOT EXISTS `variable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `short` varchar(64) NOT NULL,
  `value` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `short` (`short`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `variable`
--


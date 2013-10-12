SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `homerseklet`
--

-- --------------------------------------------------------

--
-- Table structure for table `bp-kint`
--

CREATE TABLE IF NOT EXISTS `bp-kint` (
  `date` datetime NOT NULL,
  `value` float NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bp-szoba`
--

CREATE TABLE IF NOT EXISTS `bp-szoba` (
  `date` datetime NOT NULL,
  `value` float NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tata-kint`
--

CREATE TABLE IF NOT EXISTS `tata-kint` (
  `date` datetime NOT NULL,
  `value` float NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tata-szoba`
--

CREATE TABLE IF NOT EXISTS `tata-szoba` (
  `date` datetime NOT NULL,
  `value` float NOT NULL,
  PRIMARY KEY (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

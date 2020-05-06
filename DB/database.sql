-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.37-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             10.1.0.5464
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for factions_manager
CREATE DATABASE IF NOT EXISTS `factions_manager` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `factions_manager`;

-- Dumping structure for table factions_manager.accounts
CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `steamName` varchar(50) DEFAULT NULL,
  `steamID` varchar(32) NOT NULL,
  `steampfp` text,
  `steampfpmed` text,
  `steampfplarge` text,
  `isAdmin` int(11) NOT NULL DEFAULT '0',
  `remember_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `steamid` (`steamID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table factions_manager.accounts: ~0 rows (approximately)
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;

-- Dumping structure for table factions_manager.factions
CREATE TABLE IF NOT EXISTS `factions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys` varchar(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  `rank` varchar(50) NOT NULL,
  `lastlogin` varchar(50) NOT NULL,
  `defaultRank` int(11) NOT NULL,
  `additionForm` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table factions_manager.factions: ~3 rows (approximately)
/*!40000 ALTER TABLE `factions` DISABLE KEYS */;
INSERT INTO `factions` (`id`, `sys`, `name`, `rank`, `lastlogin`, `defaultRank`, `additionForm`) VALUES
	(1, 'apc', 'Altis Police', 'coplevel', 'lastcopseen', 1, 1),
	(2, 'nhs', 'National Health Service', 'nhslevel', 'lastnhsseen', 16, 1);
/*!40000 ALTER TABLE `factions` ENABLE KEYS */;

-- Dumping structure for table factions_manager.fields
CREATE TABLE IF NOT EXISTS `fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form` int(11) NOT NULL,
  `fieldName` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `type` enum('Input','Textarea','factionRanks','factionMembers','factionSections','memberRanks','memberSections','memberRanksAbove','memberRanksBelow') NOT NULL,
  `conditions` text NOT NULL,
  `default` varchar(100) NOT NULL DEFAULT '',
  `required` int(11) NOT NULL DEFAULT '0',
  `hidden` int(11) NOT NULL DEFAULT '0',
  `system` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;

-- Dumping data for table factions_manager.fields: ~35 rows (approximately)
/*!40000 ALTER TABLE `fields` DISABLE KEYS */;
INSERT INTO `fields` (`id`, `form`, `fieldName`, `name`, `description`, `type`, `conditions`, `default`, `required`, `hidden`, `system`) VALUES
	(1, 1, 'username', 'Member\'s Name', 'This should be the name this faction member uses while in-game.', 'Input', 'placeholder="Name..."', '', 1, 0, 1),
	(2, 1, 'steamid', 'Steam ID', 'This should have been included in any application.', 'Input', 'minlength = "17" maxlength = "17" placeholder="Steam ID..."', '', 1, 0, 1),
	(3, 1, 'forumid', 'Forum ID', 'This is a 1 - 4 digit number that can be found by checking their forum profile url.', 'Input', 'minlength = "1" maxlength = "4" placeholder="Forum ID..."', '', 1, 0, 1),
	(4, 1, 'rank', 'Entry Rank', 'This faction member will be automatically whitelisted to this rank upon form completion.', 'factionRanks', '', '', 1, 0, 1),
	(5, 1, 'section', 'Section', 'This should be the section / station you wish this faction member to start in.', 'factionSections', '', '', 1, 0, 1),
	(6, 2, 'steamid', 'Steam ID', '', 'Input', '', '', 1, 1, 1),
	(7, 2, 'note', 'Note', 'Anything you wish to note down about this member write it here.', 'Textarea', '', '', 1, 0, 1),
	(9, 11, 'steamid', 'Steam ID', '', 'Input', '', '', 1, 1, 1),
	(10, 11, 'newname', 'New Name', 'Input the name you wish to rename this member to.', 'Input', '', '', 1, 0, 1),
	(11, 11, 'note', 'Note', 'Write anything you wish to note down about this name change here.', 'Textarea', '', 'N/A', 0, 0, 1),
	(12, 9, 'steamid', 'Steam ID', '', 'Input', '', '', 1, 1, 1),
	(13, 9, 'note', 'Note', 'The reasoning for the blacklist should be written here.', 'Textarea', '', '', 1, 0, 1),
	(14, 9, 'evidence', 'Evidence', 'Any evidence of should be put here.', 'Input', '', 'N/A', 0, 0, 1),
	(15, 10, 'steamid', 'Steam ID', '', 'Input', '', '', 1, 1, 1),
	(16, 10, 'note', 'Note', 'The reasoning for the unblacklist should be written here.', 'Textarea', '', '', 1, 0, 1),
	(17, 6, 'steamid', 'Steam ID', '', 'Input', '', '', 1, 1, 1),
	(18, 6, 'note', 'Note', 'The reasoning for the suspension should be written here.', 'Textarea', '', '', 1, 0, 1),
	(19, 6, 'evidence', 'Evidence', 'Any evidence of should be put here.', 'Input', '', 'N/A', 0, 0, 1),
	(20, 7, 'steamid', 'Steam ID', '', 'Input', '', '', 1, 1, 1),
	(21, 7, 'note', 'Note', 'The reasoning for the unsuspension should be written here.', 'Textarea', '', 'Contact Actioning Member', 0, 0, 1),
	(22, 12, 'steamid', 'Steam ID', '', 'Input', '', '', 1, 1, 1),
	(23, 12, 'section', 'New Section', 'Select the section you wish to trasnfer this member to.', 'memberSections', '', '', 1, 0, 1),
	(24, 12, 'note', 'Note', 'Any extra notes about this transfer should be put here.', 'Textarea', '', 'N/A', 0, 0, 1),
	(25, 5, 'steamid', 'Steam ID', '', 'Input', '', '', 1, 1, 1),
	(26, 5, 'note', 'Note', 'Any extra notes about this resignation should be put here.', 'Textarea', '', 'N/A', 0, 0, 1),
	(27, 13, 'steamid', 'Steam ID', '', 'Input', '', '', 1, 1, 1),
	(28, 13, 'rank', 'Entry Rank', 'What rank should they be added to?', 'factionRanks', '', '', 1, 0, 1),
	(29, 13, 'section', 'Section', 'What section should they be added to?', 'factionSections', '', '', 1, 0, 1),
	(30, 13, 'note', 'Note', 'Any extra notes about this unarchival should be put here.', 'Textarea', '', 'N/A', 0, 0, 1),
	(31, 8, 'steamid', 'Steam ID', '', 'Input', '', '', 1, 1, 1),
	(32, 8, 'note', 'Note', 'Reasoning for this removal should be written here.', 'Textarea', '', '', 1, 0, 1),
	(33, 3, 'steamid', 'Steam ID', '', 'Input', '', '', 1, 1, 1),
	(34, 3, 'rank', 'New Rank', 'Select the rank they should be promoted to', 'memberRanksAbove', '', '', 1, 0, 1),
	(35, 3, 'note', 'Note', 'Notes about this promotion can be entered here', 'Textarea', '', 'N/A', 0, 0, 1),
	(36, 4, 'steamid', 'Steam ID', '', 'Input', '', '', 1, 1, 1),
	(37, 4, 'rank', 'New Rank', 'Select the rank they should be demoted to', 'memberRanksBelow', '', '', 1, 0, 1),
	(38, 4, 'note', 'Note', 'Notes about this demtion can be entered here', 'Textarea', '', 'N/A', 0, 0, 1);
/*!40000 ALTER TABLE `fields` ENABLE KEYS */;

-- Dumping structure for table factions_manager.forms
CREATE TABLE IF NOT EXISTS `forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faction` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `return` varchar(200) NOT NULL DEFAULT '/',
  `method` enum('GET','POST') NOT NULL,
  `action` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `customFunction` varchar(50) NOT NULL,
  `modal` int(11) NOT NULL DEFAULT '0',
  `submitLog` int(11) NOT NULL DEFAULT '0',
  `predefinedSteamid` int(11) NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '0',
  `system` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

-- Dumping data for table factions_manager.forms: ~13 rows (approximately)
/*!40000 ALTER TABLE `forms` DISABLE KEYS */;
INSERT INTO `forms` (`id`, `faction`, `name`, `description`, `return`, `method`, `action`, `status`, `customFunction`, `modal`, `submitLog`, `predefinedSteamid`, `active`, `system`) VALUES
	(1, '', 'Member Addition Form', 'This form is used to add new members to this faction.', '{{steamid}}', 'POST', 'Added', 'Manually', 'insertMember', 0, 1, 0, 1, 1),
	(2, '', 'Note Form', 'Used to add notes to members of the faction.', '{{steamid}}', 'POST', 'Note', 'Added', '', 1, 1, 1, 1, 1),
	(3, '', 'Promotion Form', 'Allows you to promote the selected member.', '{{steamid}}', 'POST', 'Promotion', 'Completed', 'changeRank', 1, 1, 1, 1, 1),
	(4, '', 'Demotion Form', 'Allows you to demote the selected member.', '{{steamid}}', 'POST', 'Demotion', 'Completed', 'changeRank', 1, 1, 1, 1, 1),
	(5, '', 'Resignation Form', 'Allows you to resign the selected member.', 'archive/{{steamid}}', 'POST', 'Resignation', 'Completed', 'archive', 1, 1, 1, 1, 1),
	(6, '', 'Suspension Form', 'Allows you to suspend the selected member.', '{{steamid}}', 'POST', 'Suspension', 'Started', 'suspend', 1, 1, 1, 1, 1),
	(7, '', 'Unsuspension Form', 'Allows you to unsuspend the selected member.', '{{steamid}}', 'POST', 'Suspension', 'Ended', 'suspend', 1, 1, 1, 1, 1),
	(8, '', 'Fire Form', 'Allows you to fire the selected member.', 'archive/{{steamid}}', 'POST', 'Fired', 'Completed', 'archive', 1, 1, 1, 1, 1),
	(9, '', 'Blacklist Form', 'Allows you to blacklist the selected member.', 'archive/{{steamid}}', 'POST', 'Blacklist', 'Issued', 'blacklist', 1, 1, 1, 1, 1),
	(10, '', 'Unblacklist Form', 'Allows you to unblacklist the selected member.', 'archive/{{steamid}}', 'POST', 'Blacklist', 'Removed', 'blacklist', 1, 1, 1, 1, 1),
	(11, '', 'Name Change Form', 'Allows you to rename the selected member.', '{{steamid}}', 'POST', 'Name Change', 'Approved', 'renameMember', 1, 1, 1, 1, 1),
	(12, '', 'Section Transfer Form', 'Allows you to change the selected member\'s section.', '{{steamid}}', 'POST', 'Section Transfer', 'Approved', 'transfer', 1, 1, 1, 1, 1),
	(13, '', 'Unarchival Form', 'Allows you to readd this member back to the faction.', '{{steamid}}', 'POST', '	Archive Removal', 'Completed', 'unarchive', 1, 1, 1, 1, 1);
/*!40000 ALTER TABLE `forms` ENABLE KEYS */;

-- Dumping structure for table factions_manager.levels
CREATE TABLE IF NOT EXISTS `levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faction` varchar(3) NOT NULL,
  `type` enum('Access Level','Power Level') NOT NULL,
  `name` varchar(50) NOT NULL,
  `level` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table factions_manager.levels: ~0 rows (approximately)
/*!40000 ALTER TABLE `levels` DISABLE KEYS */;
INSERT INTO `levels` (`id`, `faction`, `type`, `name`, `level`) VALUES
	(1, 'apc', 'Power Level', 'Bronze Command', 1);
/*!40000 ALTER TABLE `levels` ENABLE KEYS */;

-- Dumping structure for table factions_manager.logs
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faction` varchar(3) NOT NULL,
  `member` varchar(50) NOT NULL,
  `actioner` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table factions_manager.logs: ~0 rows (approximately)
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;

-- Dumping structure for table factions_manager.members
CREATE TABLE IF NOT EXISTS `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faction` varchar(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  `steamid` varchar(32) NOT NULL,
  `forumid` varchar(4) NOT NULL,
  `section` varchar(50) DEFAULT NULL,
  `mainlevel` int(11) NOT NULL DEFAULT '1',
  `isSuspended` int(11) NOT NULL DEFAULT '0',
  `isBlacklisted` int(11) NOT NULL DEFAULT '0',
  `isLOA` int(11) NOT NULL DEFAULT '0',
  `isHoliday` int(11) NOT NULL DEFAULT '0',
  `isArchive` int(11) NOT NULL DEFAULT '0',
  `isBlocked` int(11) NOT NULL DEFAULT '0',
  `joindate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_rank_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table factions_manager.members: ~12 rows (approximately)
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` (`id`, `faction`, `name`, `steamid`, `forumid`, `section`, `mainlevel`, `isSuspended`, `isBlacklisted`, `isLOA`, `isHoliday`, `isArchive`, `isBlocked`, `joindate`, `last_rank_change`, `last_login`) VALUES
	(1, 'apc', 'EXAMPLE USER', 'EXAMPLE USER', '4818', 'Command', 14, 0, 0, 0, 0, 0, 0, '2019-09-07 17:07:20', '2019-09-07 17:07:20', '2019-09-28 21:40:28'),
	(2, 'nhs', 'EXAMPLE USER', 'EXAMPLE USER', '4818', 'NHS Command', 23, 0, 0, 0, 0, 0, 0, '2019-09-07 17:07:20', '2019-09-07 17:07:20', '2019-09-28 21:40:28');
/*!40000 ALTER TABLE `members` ENABLE KEYS */;

-- Dumping structure for table factions_manager.powers
CREATE TABLE IF NOT EXISTS `powers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faction` varchar(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  `form` int(11) NOT NULL,
  `colour` varchar(50) NOT NULL DEFAULT '28a645',
  `suspended` int(11) NOT NULL DEFAULT '0',
  `archived` int(11) NOT NULL DEFAULT '0',
  `blacklisted` int(11) NOT NULL DEFAULT '0',
  `system` int(11) NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- Dumping data for table factions_manager.powers: ~12 rows (approximately)
/*!40000 ALTER TABLE `powers` DISABLE KEYS */;
INSERT INTO `powers` (`id`, `faction`, `name`, `form`, `colour`, `suspended`, `archived`, `blacklisted`, `system`, `active`) VALUES
	(1, '', 'Add Note', 2, '28a645', 0, 0, 0, 1, 1),
	(2, '', 'Promote', 3, '4c1031', 0, 0, 0, 1, 1),
	(3, '', 'Demote', 4, '660000', 0, 0, 0, 1, 1),
	(4, '', 'Resign', 5, '0f4142', 0, 0, 0, 1, 1),
	(5, '', 'Suspend', 6, '351d74', 0, 0, 0, 1, 1),
	(6, '', 'Unsuspend', 7, '351d74', 1, 0, 0, 1, 1),
	(7, '', 'Fire', 8, 'cc0001', 2, 0, 0, 1, 1),
	(8, '', 'Blacklist', 9, '222222', 0, 1, 0, 1, 1),
	(9, '', 'Unblacklist', 10, '222222', 0, 1, 1, 1, 1),
	(10, '', 'Change Name', 11, '61a1d9', 0, 0, 0, 1, 1),
	(11, '', 'Transfer Section', 12, '00d27f', 0, 0, 0, 1, 1),
	(12, '', 'Unarchive', 13, '00d27f', 0, 1, 0, 1, 1);
/*!40000 ALTER TABLE `powers` ENABLE KEYS */;

-- Dumping structure for table factions_manager.ranks
CREATE TABLE IF NOT EXISTS `ranks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faction` varchar(3) NOT NULL,
  `sName` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `level` int(11) NOT NULL DEFAULT '0',
  `system` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;

-- Dumping data for table factions_manager.ranks: ~23 rows (approximately)
/*!40000 ALTER TABLE `ranks` DISABLE KEYS */;
INSERT INTO `ranks` (`id`, `faction`, `sName`, `name`, `level`, `system`) VALUES
	(1, 'apc', 'N/A', 'N/A', 0, 1),
	(2, 'apc', 'CSO', 'Community Support Officer', 1, 0),
	(3, 'apc', 'PCSO', 'Police Community Support Officer', 2, 0),
	(4, 'apc', 'PC', 'Police Constable', 3, 0),
	(5, 'apc', 'SPC', 'Senior Police Constable', 4, 0),
	(6, 'apc', 'SGT', 'Sergeant', 5, 0),
	(7, 'apc', 'INS', 'Inspector', 6, 0),
	(8, 'apc', 'CI', 'Chief Inspector', 7, 0),
	(9, 'apc', 'CMDR', 'Commander', 8, 0),
	(10, 'apc', 'SI', 'Superintendent', 9, 0),
	(11, 'apc', 'CSI', 'Chief Superintendent', 10, 0),
	(12, 'apc', 'AC', 'Assistant Commissioner', 11, 0),
	(13, 'apc', 'DC', 'Deputy Commissioner', 12, 0),
	(14, 'apc', 'COM', 'Commissioner', 13, 0),
	(15, 'nhs', 'N/A', 'N/A', 0, 1),
	(16, 'nhs', 'STU', 'Student', 1, 0),
	(17, 'nhs', 'FA', 'First Aider', 2, 0),
	(18, 'nhs', 'PAR', 'Paramedic', 3, 0),
	(19, 'nhs', 'DOC', 'Doctor', 4, 0),
	(20, 'nhs', 'GP', 'General Practitioner', 5, 0),
	(21, 'nhs', 'SUR', 'Surgeon', 6, 0),
	(22, 'nhs', 'CST', 'Consultant', 7, 0),
	(23, 'nhs', 'CMO', 'Chief Medical Officer', 8, 0);
/*!40000 ALTER TABLE `ranks` ENABLE KEYS */;

-- Dumping structure for table factions_manager.responses
CREATE TABLE IF NOT EXISTS `responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table factions_manager.responses: ~0 rows (approximately)
/*!40000 ALTER TABLE `responses` DISABLE KEYS */;
/*!40000 ALTER TABLE `responses` ENABLE KEYS */;

-- Dumping structure for table factions_manager.sections
CREATE TABLE IF NOT EXISTS `sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faction` varchar(50) NOT NULL DEFAULT 'apc',
  `shortName` varchar(3) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `type` enum('Normal','Academy') NOT NULL DEFAULT 'Normal',
  `prefix` int(11) DEFAULT '0',
  `system` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- Dumping data for table factions_manager.sections: ~9 rows (approximately)
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` (`id`, `faction`, `shortName`, `name`, `type`, `prefix`, `system`) VALUES
	(1, 'apc', 'ST', 'Staff', 'Normal', 0, 1),
	(2, 'apc', 'CO', 'Command', 'Normal', 1, 0),
	(3, 'apc', 'KV', 'Kavala Station', 'Normal', 1, 0),
	(4, 'apc', 'AR', 'Archive', 'Normal', 1, 1),
	(5, 'apc', 'AT', 'Athira Station', 'Normal', 1, 0),
	(6, 'nhs', 'CO', 'NHS Command', 'Normal', 0, 0),
	(7, 'nhs', 'KV', 'Kavala', 'Normal', 0, 0),
	(8, 'nhs', 'AT', 'Athira', 'Normal', 0, 0),
	(9, 'apc', 'AC', 'Academy', 'Academy', 1, 0);
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;

-- Dumping structure for table factions_manager.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  `time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table factions_manager.settings: ~1 rows (approximately)
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` (`id`, `name`, `value`, `time`) VALUES
	(1, 'tasksrun', 0, '00:00:00');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;

-- Dumping structure for table factions_manager.subpages
CREATE TABLE IF NOT EXISTS `subpages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faction` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `subdirectory` varchar(50) NOT NULL,
  `active` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

-- Dumping data for table factions_manager.subpages: ~8 rows (approximately)
/*!40000 ALTER TABLE `subpages` DISABLE KEYS */;
INSERT INTO `subpages` (`id`, `faction`, `name`, `subdirectory`, `active`) VALUES
	(1, 'apc', 'PNC', 'apc/', 1),
	(2, 'nhs', 'Interpol', 'nhs/', 1),
	(3, 'apc', 'Archive', 'apc/archive', 1),
	(4, 'nhs', 'Archive', 'nhs/archive', 1),
	(5, 'apc', 'Custom Search', 'apc/search', 1),
	(6, 'nhs', 'Custom Search', 'nhs/search', 1),
	(7, 'apc', 'Statistics', 'apc/stats', 1),
	(8, 'nhs', 'Statistics', 'nhs/stats', 1);
/*!40000 ALTER TABLE `subpages` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

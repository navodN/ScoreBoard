-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.42 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for vits
CREATE DATABASE IF NOT EXISTS `vits` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `vits`;

-- Dumping structure for table vits.batting_scores
CREATE TABLE IF NOT EXISTS `batting_scores` (
  `score_id` int NOT NULL AUTO_INCREMENT,
  `player_id` int NOT NULL,
  `position` int DEFAULT NULL,
  `is_batted` tinyint DEFAULT '0',
  `runs` int DEFAULT '0',
  `balls_faced` int DEFAULT '0',
  `fours` int DEFAULT '0',
  `sixes` int DEFAULT '0',
  `is_out` tinyint(1) DEFAULT '0',
  `dismissal_type` varchar(50) DEFAULT NULL,
  `dismissal_status` varchar(200) DEFAULT NULL,
  `bowled_by` int DEFAULT NULL,
  `caught_by` int DEFAULT NULL,
  PRIMARY KEY (`score_id`),
  KEY `player_id` (`player_id`),
  KEY `bowled_by` (`bowled_by`),
  KEY `caught_by` (`caught_by`),
  CONSTRAINT `batting_scores_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `players` (`player_id`),
  CONSTRAINT `batting_scores_ibfk_3` FOREIGN KEY (`bowled_by`) REFERENCES `players` (`player_id`),
  CONSTRAINT `batting_scores_ibfk_4` FOREIGN KEY (`caught_by`) REFERENCES `players` (`player_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table vits.batting_scores: ~10 rows (approximately)
INSERT INTO `batting_scores` (`score_id`, `player_id`, `position`, `is_batted`, `runs`, `balls_faced`, `fours`, `sixes`, `is_out`, `dismissal_type`, `dismissal_status`, `bowled_by`, `caught_by`) VALUES
	(2, 2, 2, 1, 4, 1, 1, 0, 0, '', 'not out', NULL, NULL),
	(3, 3, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
	(4, 4, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
	(5, 5, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
	(6, 6, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
	(7, 7, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
	(8, 8, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
	(9, 9, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
	(10, 10, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
	(14, 1, 1, 1, 1, 1, 0, 0, 0, '', 'not out', NULL, NULL);

-- Dumping structure for table vits.bowling_figures
CREATE TABLE IF NOT EXISTS `bowling_figures` (
  `figure_id` int NOT NULL AUTO_INCREMENT,
  `player_id` int NOT NULL,
  `bowling_ord` int DEFAULT '0',
  `is_bowled` int DEFAULT '0',
  `overs` decimal(5,1) DEFAULT '0.0',
  `maidens` int DEFAULT '0',
  `runs_conceded` int DEFAULT '0',
  `wickets` int DEFAULT '0',
  `wides` int DEFAULT '0',
  `no_balls` int DEFAULT '0',
  `dots` int DEFAULT '0',
  PRIMARY KEY (`figure_id`),
  KEY `player_id` (`player_id`),
  CONSTRAINT `bowling_figures_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `players` (`player_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table vits.bowling_figures: ~1 rows (approximately)
INSERT INTO `bowling_figures` (`figure_id`, `player_id`, `bowling_ord`, `is_bowled`, `overs`, `maidens`, `runs_conceded`, `wickets`, `wides`, `no_balls`, `dots`) VALUES
	(2, 14, 2, 1, 5.1, 2, 45, 2, 2, 2, 2),
	(4, 1, 1, 1, 1.0, 1, 1, 1, 1, 1, 1),
	(5, 2, 2, 1, 1.0, 1, 1, 1, 1, 1, 1),
	(6, 12, 1, 1, 1.0, 1, 10, 1, 1, 1, 1);

-- Dumping structure for table vits.innings
CREATE TABLE IF NOT EXISTS `innings` (
  `innings_id` int NOT NULL AUTO_INCREMENT,
  `match_id` int NOT NULL,
  `batting_team_id` int NOT NULL,
  `bowling_team_id` int NOT NULL,
  `total_runs` int DEFAULT '0',
  `total_wickets` int DEFAULT '0',
  `overs_played` decimal(5,1) DEFAULT '0.0',
  `extras` int DEFAULT '0',
  `innings_number` tinyint NOT NULL,
  PRIMARY KEY (`innings_id`),
  KEY `match_id` (`match_id`),
  KEY `batting_team_id` (`batting_team_id`),
  KEY `bowling_team_id` (`bowling_team_id`),
  CONSTRAINT `innings_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`match_id`),
  CONSTRAINT `innings_ibfk_2` FOREIGN KEY (`batting_team_id`) REFERENCES `teams` (`team_id`),
  CONSTRAINT `innings_ibfk_3` FOREIGN KEY (`bowling_team_id`) REFERENCES `teams` (`team_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table vits.innings: ~0 rows (approximately)

-- Dumping structure for table vits.matches
CREATE TABLE IF NOT EXISTS `matches` (
  `match_id` int NOT NULL AUTO_INCREMENT,
  `home_team_id` int NOT NULL,
  `away_team_id` int NOT NULL,
  `status` enum('upcoming','live','completed') DEFAULT 'upcoming',
  `winner_team_id` int DEFAULT NULL,
  `match_summary` text,
  `toss_winner_id` int DEFAULT NULL,
  `toss_decision` enum('bat','bowl') DEFAULT NULL,
  PRIMARY KEY (`match_id`),
  KEY `home_team_id` (`home_team_id`),
  KEY `away_team_id` (`away_team_id`),
  KEY `winner_team_id` (`winner_team_id`),
  KEY `toss_winner_id` (`toss_winner_id`),
  CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`home_team_id`) REFERENCES `teams` (`team_id`),
  CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`away_team_id`) REFERENCES `teams` (`team_id`),
  CONSTRAINT `matches_ibfk_3` FOREIGN KEY (`winner_team_id`) REFERENCES `teams` (`team_id`),
  CONSTRAINT `matches_ibfk_4` FOREIGN KEY (`toss_winner_id`) REFERENCES `teams` (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table vits.matches: ~0 rows (approximately)
INSERT INTO `matches` (`match_id`, `home_team_id`, `away_team_id`, `status`, `winner_team_id`, `match_summary`, `toss_winner_id`, `toss_decision`) VALUES
	(1, 1, 2, 'upcoming', NULL, NULL, NULL, NULL);

-- Dumping structure for table vits.match_events
CREATE TABLE IF NOT EXISTS `match_events` (
  `event_id` int NOT NULL AUTO_INCREMENT,
  `match_id` int NOT NULL,
  `innings_id` int NOT NULL,
  `over_number` decimal(5,1) NOT NULL,
  `ball_number` int NOT NULL,
  `event_type` enum('run','wicket','boundary','extra') NOT NULL,
  `batsman_id` int NOT NULL,
  `bowler_id` int NOT NULL,
  `runs` int DEFAULT '0',
  `extra_type` enum('wide','no_ball','bye','leg_bye') DEFAULT NULL,
  `extra_runs` int DEFAULT '0',
  `wicket_type` varchar(50) DEFAULT NULL,
  `fielder_id` int DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`),
  KEY `match_id` (`match_id`),
  KEY `innings_id` (`innings_id`),
  KEY `batsman_id` (`batsman_id`),
  KEY `bowler_id` (`bowler_id`),
  KEY `fielder_id` (`fielder_id`),
  CONSTRAINT `match_events_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`match_id`),
  CONSTRAINT `match_events_ibfk_2` FOREIGN KEY (`innings_id`) REFERENCES `innings` (`innings_id`),
  CONSTRAINT `match_events_ibfk_3` FOREIGN KEY (`batsman_id`) REFERENCES `players` (`player_id`),
  CONSTRAINT `match_events_ibfk_4` FOREIGN KEY (`bowler_id`) REFERENCES `players` (`player_id`),
  CONSTRAINT `match_events_ibfk_5` FOREIGN KEY (`fielder_id`) REFERENCES `players` (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table vits.match_events: ~0 rows (approximately)

-- Dumping structure for table vits.players
CREATE TABLE IF NOT EXISTS `players` (
  `player_id` int NOT NULL AUTO_INCREMENT,
  `team_id` int DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `batting_style` varchar(50) DEFAULT NULL,
  `bowling_style` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`player_id`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `players_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table vits.players: ~22 rows (approximately)
INSERT INTO `players` (`player_id`, `team_id`, `first_name`, `last_name`, `date_of_birth`, `role`, `batting_style`, `bowling_style`) VALUES
	(1, 1, 'Rohit', 'Sharma 11', '1987-04-30', 'Opening Batsman', 'Right-handed', 'Right-arm offbreak'),
	(2, 1, 'Shubman', 'Gill', '1999-09-08', 'Opening Batsman', 'Right-handed', 'Right-arm offbreak'),
	(3, 1, 'Virat', 'Kohli', '1988-11-05', 'Top Order Batsman', 'Right-handed', 'Right-arm medium'),
	(4, 1, 'Steve', 'Smith', '1989-06-02', 'Top Order Batsman', 'Right-handed', 'Right-arm legbreak'),
	(5, 1, 'Kane', 'Williamson', '1990-08-08', 'Top Order Batsman', 'Right-handed', 'Right-arm offbreak'),
	(6, 1, 'Joe', 'Root', '1990-12-30', 'Middle Order Batsman', 'Right-handed', 'Right-arm offbreak'),
	(7, 1, 'Babar', 'Azam', '1994-10-15', 'Middle Order Batsman', 'Right-handed', 'Right-arm offbreak'),
	(8, 1, 'Quinton', 'de Kock', '1992-12-17', 'Middle Order Batsman/Wicketkeeper', 'Left-handed', NULL),
	(9, 1, 'Ben', 'Stokes', '1991-06-04', 'All-rounder', 'Left-handed', 'Right-arm fast-medium'),
	(10, 1, 'Ravindra', 'Jadeja', '1988-12-06', 'All-rounder', 'Left-handed', 'Left-arm orthodox'),
	(11, 1, 'Glenn', 'Maxwell', '1988-10-14', 'All-rounder', 'Right-handed', 'Right-arm offbreak'),
	(12, 2, 'David', 'Warner', '1986-10-27', 'Opening Batsman', 'Left-handed', 'Right-arm legbreak'),
	(13, 2, 'KL', 'Rahul', '1992-04-18', 'Opening Batsman', 'Right-handed', NULL),
	(14, 2, 'Kane', 'Williamson', '1990-08-08', 'Top Order Batsman', 'Right-handed', 'Right-arm offbreak'),
	(15, 2, 'Ross', 'Taylor', '1984-03-08', 'Top Order Batsman', 'Right-handed', 'Right-arm offbreak'),
	(16, 2, 'Faf', 'du Plessis', '1984-07-13', 'Top Order Batsman', 'Right-handed', 'Right-arm legbreak'),
	(17, 2, 'Jos', 'Buttler', '1990-09-08', 'Middle Order Batsman/Wicketkeeper', 'Right-handed', NULL),
	(18, 2, 'Shakib', 'Al Hasan', '1987-03-24', 'Middle Order Batsman', 'Left-handed', 'Left-arm orthodox'),
	(19, 2, 'Angelo', 'Mathews', '1987-06-02', 'Middle Order Batsman', 'Right-handed', 'Right-arm fast-medium'),
	(20, 2, 'Hardik', 'Pandya', '1993-10-11', 'All-rounder', 'Right-handed', 'Right-arm fast-medium'),
	(21, 2, 'Jason', 'Holder', '1991-11-05', 'All-rounder', 'Right-handed', 'Right-arm fast-medium'),
	(22, 2, 'Moeen', 'Ali', '1987-06-18', 'All-rounder', 'Left-handed', 'Right-arm offbreak');

-- Dumping structure for table vits.teams
CREATE TABLE IF NOT EXISTS `teams` (
  `team_id` int NOT NULL AUTO_INCREMENT,
  `team_name` varchar(100) NOT NULL,
  PRIMARY KEY (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table vits.teams: ~2 rows (approximately)
INSERT INTO `teams` (`team_id`, `team_name`) VALUES
	(1, 'St. Sylvester\'s'),
	(2, 'Vidyartha');

-- Dumping structure for table vits.users
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table vits.users: ~0 rows (approximately)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

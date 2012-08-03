-- SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 03, 2012 at 10:08 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `go`
--

-- --------------------------------------------------------

--
-- Table structure for table `core_notifications`
--

CREATE TABLE `core_notifications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `owner_id` int(11) unsigned DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text,
  `class` varchar(255) DEFAULT NULL,
  `is_pinned` tinyint(1) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `core_notifications`
--


-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `role` varchar(32) NOT NULL DEFAULT '',
  `resource` varchar(32) NOT NULL DEFAULT '',
  `privilege` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`role`,`resource`,`privilege`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` VALUES('admin', 'user', 'edit');
INSERT INTO `user_permissions` VALUES('guest', 'password', 'recover');
INSERT INTO `user_permissions` VALUES('guest', 'profile', 'create');
INSERT INTO `user_permissions` VALUES('user', 'profile', 'logout');
INSERT INTO `user_permissions` VALUES('user', 'profile', 'view');
INSERT INTO `user_permissions` VALUES('user', 'user', 'view');

-- --------------------------------------------------------

--
-- Table structure for table `user_users`
--

CREATE TABLE `user_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `owner_id` int(11) unsigned DEFAULT NULL,
  `email` varchar(64) NOT NULL,
  `password_hash` char(32) NOT NULL,
  `password_salt` char(4) NOT NULL,
  `role` enum('admin','user','guest','non_approved_user') NOT NULL DEFAULT 'guest',
  `active_at` datetime DEFAULT NULL,
  `settings` text,
  `photo` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `user_users`
--

INSERT INTO `user_users` VALUES(1, 'zelibobla', '2012-07-31 16:11:44', '2012-07-31 16:11:44', 1, 1, 'zelibobla@gmail.com', '6dd9c9f45855d3d3df1b954c28f5aa95', 'zthR', 'admin', '2012-08-03 22:08:39', NULL, 'a_7e33c60b.jpg');

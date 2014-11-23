-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Ноя 24 2014 г., 04:18
-- Версия сервера: 5.5.35
-- Версия PHP: 5.5.15-1~dotdeb.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `38studio`
--

-- --------------------------------------------------------

--
-- Структура таблицы `solutions`
--

CREATE TABLE IF NOT EXISTS `solutions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `linkid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `group` int(11) NOT NULL,
  `organization` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `industry` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `functions` varchar(255) DEFAULT NULL,
  `arms` int(11) NOT NULL,
  `date` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `reviewtext` text,
  `revdocs` text,
  `raw` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `linkid` (`linkid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

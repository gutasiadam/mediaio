-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: mysql
-- Létrehozás ideje: 2023. Aug 29. 11:45
-- Kiszolgáló verziója: 5.7.8-rc
-- PHP verzió: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `arpadmedia`
--
CREATE DATABASE IF NOT EXISTS `arpadmedia` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `arpadmedia`;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `budget`
--

CREATE TABLE IF NOT EXISTS `budget` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary index',
  `TableID` tinyint(4) DEFAULT NULL COMMENT 'Table (0: Media, 1: Egyesulet)',
  `Date` date DEFAULT NULL,
  `Value` int(11) DEFAULT NULL,
  `Name` tinytext COMMENT 'Tetel neve',
  `Data` json NOT NULL COMMENT 'Edited by, Comments',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Budgeting table';

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `eventprep`
--

CREATE TABLE IF NOT EXISTS `eventprep` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_hungarian_ci NOT NULL,
  `date_Created` date NOT NULL,
  `start_event` datetime NOT NULL,
  `end_event` datetime NOT NULL,
  `borderColor` text COLLATE utf8_hungarian_ci NOT NULL,
  `secureId` text COLLATE utf8_hungarian_ci NOT NULL,
  `user` text COLLATE utf8_hungarian_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `start_event` datetime NOT NULL,
  `end_event` datetime NOT NULL,
  `add_Date` date NOT NULL,
  `borderColor` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `feladatok`
--

CREATE TABLE IF NOT EXISTS `feladatok` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Datum` date NOT NULL,
  `Szemely1` varchar(50) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `Szemely2` varchar(50) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `Szemely1_Status` char(1) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `Szemely2_Status` char(1) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `leltar`
--

CREATE TABLE IF NOT EXISTS `leltar` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UID` text COLLATE utf8mb4_hungarian_ci NOT NULL,
  `Nev` text COLLATE utf8mb4_hungarian_ci,
  `ConnectsToItems` json DEFAULT NULL,
  `Tipus` text COLLATE utf8mb4_hungarian_ci,
  `Category` tinytext COLLATE utf8mb4_hungarian_ci,
  `Status` int(11) DEFAULT '1',
  `RentBy` text COLLATE utf8mb4_hungarian_ci,
  `TakeRestrict` text COLLATE utf8mb4_hungarian_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `takelog`
--

CREATE TABLE IF NOT EXISTS `takelog` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` text CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL,
  `User` text CHARACTER SET utf8 NOT NULL,
  `Items` json NOT NULL,
  `Event` text CHARACTER SET utf8 NOT NULL,
  `Acknowledged` tinyint(1) NOT NULL DEFAULT '0',
  `ACKBY` varchar(20) COLLATE utf8_hungarian_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `takeoutpresets`
--

CREATE TABLE IF NOT EXISTS `takeoutpresets` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` tinytext NOT NULL COMMENT 'Name of the preset',
  `Items` json NOT NULL COMMENT 'Items inside the preset',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `idUsers` int(11) NOT NULL AUTO_INCREMENT,
  `usernameUsers` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `firstName` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `lastName` text COLLATE utf8_unicode_ci NOT NULL,
  `teleNum` text COLLATE utf8_unicode_ci NOT NULL,
  `emailUsers` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `pwdUsers` longtext COLLATE utf8_unicode_ci NOT NULL,
  `Userrole` text COLLATE utf8_unicode_ci NOT NULL,
  `AdditionalData` json DEFAULT NULL,
  `UserPoints` decimal(10,2) NOT NULL,
  `token` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`idUsers`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `worksheet`
--

CREATE TABLE IF NOT EXISTS `worksheet` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `EventID` text COLLATE utf8_hungarian_ci NOT NULL,
  `FullName` text COLLATE utf8_hungarian_ci NOT NULL,
  `Worktype` text COLLATE utf8_hungarian_ci NOT NULL,
  `Location` text COLLATE utf8_hungarian_ci NOT NULL,
  `Comment` text COLLATE utf8_hungarian_ci,
  `RecordDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci COMMENT='This Table holds ALL Worksheet data';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

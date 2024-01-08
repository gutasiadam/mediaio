-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: mysql
-- Létrehozás ideje: 2023. Dec 02. 19:32
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
-- Adatbázis: `mediaio`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `budget`
--

CREATE TABLE `budget` (
  `ID` int(11) NOT NULL COMMENT 'Primary index',
  `TableID` tinyint(4) DEFAULT NULL COMMENT 'Table (0: Media, 1: Egyesulet)',
  `Date` date DEFAULT NULL,
  `Value` int(11) DEFAULT NULL,
  `Name` tinytext COMMENT 'Tetel neve',
  `Data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Edited by, Comments'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Budgeting table';

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `feladatok`
--

CREATE TABLE `feladatok` (
  `ID` int(11) NOT NULL,
  `Datum` date NOT NULL COMMENT 'Task date',
  `Szemely1` varchar(50) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'Applied user#1 username',
  `Szemely2` varchar(50) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'Applied user#2 username',
  `Szemely1_Status` char(1) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'User#1 task status',
  `Szemely2_Status` char(1) COLLATE utf8_hungarian_ci DEFAULT NULL COMMENT 'User#2 task status'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `formanswers`
--

CREATE TABLE `formanswers` (
  `ID` int(11) NOT NULL,
  `FormID` int(11) NOT NULL COMMENT 'Form ID',
  `userID` int(11) NOT NULL COMMENT 'User ID',
  `UserAnswers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'user Answers in JSON'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `forms`
--

CREATE TABLE `forms` (
  `ID` int(11) NOT NULL COMMENT 'Form ID',
  `Name` text COMMENT 'Form Name',
  `Status` char(1) DEFAULT NULL COMMENT 'Status:\r\nNULL: newly created\r\ne: Editing\r\n1: Accepts responses\r\n0: closed\r\n',
  `AccessRestrict` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON that contains access restrictions. If NULL, form is public',
  `Data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Form Data'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `leltar`
--

CREATE TABLE `leltar` (
  `ID` int(11) NOT NULL,
  `UID` text COLLATE utf8mb4_hungarian_ci NOT NULL COMMENT 'Item UID',
  `Nev` text COLLATE utf8mb4_hungarian_ci COMMENT 'Item name',
  `Tipus` text COLLATE utf8mb4_hungarian_ci COMMENT 'Item type',
  `Category` tinytext COLLATE utf8mb4_hungarian_ci COMMENT 'Item category',
  `Status` int(11) DEFAULT '1' COMMENT 'Item status',
  `RentBy` text COLLATE utf8mb4_hungarian_ci COMMENT 'Rented by (user)',
  `TakeRestrict` text COLLATE utf8mb4_hungarian_ci NOT NULL COMMENT 'Restrictions for taking out item',
  `ConnectsToItems` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Additional JSON data. Item can have connected items, that are automatically selected on parent selection.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `takelog`
--

CREATE TABLE `takelog` (
  `ID` int(11) NOT NULL,
  `Date` text CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL,
  `User` text CHARACTER SET utf8 NOT NULL,
  `Items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `Event` text CHARACTER SET utf8 NOT NULL,
  `Acknowledged` tinyint(1) NOT NULL DEFAULT '0',
  `ACKBY` varchar(20) COLLATE utf8_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `takeoutpresets`
--

CREATE TABLE `takeoutpresets` (
  `ID` int(11) NOT NULL,
  `Name` tinytext NOT NULL COMMENT 'Name of the preset',
  `Items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Items inside the preset'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `idUsers` int(11) NOT NULL COMMENT 'User id. Autoincremented.',
  `usernameUsers` tinytext COLLATE utf8_unicode_ci NOT NULL COMMENT 'Username',
  `firstName` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'User firstname',
  `lastName` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'User lastname',
  `teleNum` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'User phone number',
  `emailUsers` tinytext COLLATE utf8_unicode_ci NOT NULL COMMENT 'User e-mail address',
  `pwdUsers` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT 'User password',
  `Userrole` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'User Role # - deprecated',
  `AdditionalData` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'User JSON data',
  `UserPoints` decimal(10,2) NOT NULL COMMENT 'User Points',
  `token` text COLLATE utf8_unicode_ci COMMENT 'User password recovery token',
  `APIKey` varchar(2048) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL COMMENT 'REST Api Key'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `worksheet`
--

CREATE TABLE `worksheet` (
  `ID` int(11) NOT NULL,
  `EventID` text COLLATE utf8_hungarian_ci NOT NULL COMMENT 'Google Calendar Event ID',
  `FullName` text COLLATE utf8_hungarian_ci NOT NULL COMMENT 'Work name',
  `Worktype` text COLLATE utf8_hungarian_ci NOT NULL COMMENT 'Work type - deprecated',
  `Location` text COLLATE utf8_hungarian_ci NOT NULL COMMENT 'Storage - file location',
  `Link` text COLLATE utf8_hungarian_ci COMMENT 'Storage - direct read only link',
  `Comment` text COLLATE utf8_hungarian_ci COMMENT 'Additional optinal comment',
  `RecordDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci COMMENT='This Table holds ALL Worksheet data';

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `budget`
--
ALTER TABLE `budget`
  ADD PRIMARY KEY (`ID`);

--
-- A tábla indexei `feladatok`
--
ALTER TABLE `feladatok`
  ADD PRIMARY KEY (`ID`);

--
-- A tábla indexei `formanswers`
--
ALTER TABLE `formanswers`
  ADD PRIMARY KEY (`ID`);

--
-- A tábla indexei `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`ID`);

--
-- A tábla indexei `leltar`
--
ALTER TABLE `leltar`
  ADD PRIMARY KEY (`ID`);

--
-- A tábla indexei `takelog`
--
ALTER TABLE `takelog`
  ADD PRIMARY KEY (`ID`);

--
-- A tábla indexei `takeoutpresets`
--
ALTER TABLE `takeoutpresets`
  ADD PRIMARY KEY (`ID`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`idUsers`);

--
-- A tábla indexei `worksheet`
--
ALTER TABLE `worksheet`
  ADD PRIMARY KEY (`ID`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `budget`
--
ALTER TABLE `budget`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary index';

--
-- AUTO_INCREMENT a táblához `feladatok`
--
ALTER TABLE `feladatok`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `formanswers`
--
ALTER TABLE `formanswers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `forms`
--
ALTER TABLE `forms`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Form ID';

--
-- AUTO_INCREMENT a táblához `leltar`
--
ALTER TABLE `leltar`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `takelog`
--
ALTER TABLE `takelog`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `takeoutpresets`
--
ALTER TABLE `takeoutpresets`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `idUsers` int(11) NOT NULL AUTO_INCREMENT COMMENT 'User id. Autoincremented.';

--
-- AUTO_INCREMENT a táblához `worksheet`
--
ALTER TABLE `worksheet`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

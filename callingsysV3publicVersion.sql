-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2024-09-13 16:26:19
-- 服务器版本： 8.0.36
-- PHP 版本： 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `callingsys`
--

-- --------------------------------------------------------

--
-- 表的结构 `additional_records`
--

CREATE TABLE `additional_records` (
  `ID` int NOT NULL,
  `Customer_ID` int DEFAULT NULL,
  `Important_Remarks` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Package` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Appointment_Date_Time` datetime DEFAULT NULL,
  `Agent_Name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Customer_Spoken` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Agent_Remarks` text COLLATE utf8mb4_general_ci,
  `Status` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Added_Date_Time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `agents`
--

CREATE TABLE `agents` (
  `ID` int NOT NULL,
  `Agent_Name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Remarks` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `agents`
--

INSERT INTO `agents` (`ID`, `Agent_Name`, `Password`, `Remarks`) VALUES
(1, 'TEST', 'TEST123', 'TESTACC');

-- --------------------------------------------------------

--
-- 表的结构 `customers`
--

CREATE TABLE `customers` (
  `ID` int NOT NULL,
  `Name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `HP_Number` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `Age` int NOT NULL,
  `Nationality` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Language` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Status` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `Agent` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转储表的索引
--

--
-- 表的索引 `additional_records`
--
ALTER TABLE `additional_records`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Customer_ID` (`Customer_ID`);

--
-- 表的索引 `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Agent_Name` (`Agent_Name`);

--
-- 表的索引 `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`ID`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `additional_records`
--
ALTER TABLE `additional_records`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `agents`
--
ALTER TABLE `agents`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `customers`
--
ALTER TABLE `customers`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- 限制导出的表
--

--
-- 限制表 `additional_records`
--
ALTER TABLE `additional_records`
  ADD CONSTRAINT `fk_customer` FOREIGN KEY (`Customer_ID`) REFERENCES `customers` (`ID`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

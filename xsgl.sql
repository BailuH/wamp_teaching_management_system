CREATE DATABASE  IF NOT EXISTS `xsgl` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `xsgl`;
-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: xsgl
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `js`
--

DROP TABLE IF EXISTS `js`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `js` (
  `gh` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '工号',
  `xm` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '姓名',
  `xb` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '性别',
  `zc` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '职称',
  `yx` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '院系',
  PRIMARY KEY (`gh`),
  CONSTRAINT `js_chk_1` CHECK ((`xb` in (_utf8mb4'男',_utf8mb4'女')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `js`
--

LOCK TABLES `js` WRITE;
/*!40000 ALTER TABLE `js` DISABLE KEYS */;
INSERT INTO `js` VALUES ('t2001','李同树','男','教授','计算机科学系'),('t2005','黄平','女','教授','计算机科学系'),('t3009','高大全','男','讲师','计算机与自动化'),('t3013','周进和','男','副教授','计算机与自动化'),('t4010','白厚宽','男','讲师','基础部'),('t4012','马云','女','副教授','基础部');
/*!40000 ALTER TABLE `js` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kc`
--

DROP TABLE IF EXISTS `kc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kc` (
  `kcbh` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kcm` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rkjs` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '任课教师',
  PRIMARY KEY (`kcbh`),
  KEY `kc_js` (`rkjs`),
  CONSTRAINT `kc_js` FOREIGN KEY (`rkjs`) REFERENCES `js` (`gh`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kc`
--

LOCK TABLES `kc` WRITE;
/*!40000 ALTER TABLE `kc` DISABLE KEYS */;
INSERT INTO `kc` VALUES ('a101','数据库','t2001'),('a104','操作系统','t2005'),('a106','数据结构','t2001'),('a110','计算机网络','t2005'),('a111','软件工程','t2001'),('b110','高等数学','t4012'),('c022','电子线路','t3009'),('c023','计算机原理','t3013');
/*!40000 ALTER TABLE `kc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xk`
--

DROP TABLE IF EXISTS `xk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `xk` (
  `xh` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kcbh` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cj` float DEFAULT NULL,
  PRIMARY KEY (`xh`,`kcbh`),
  KEY `kcbh` (`kcbh`),
  CONSTRAINT `xk_ibfk_1` FOREIGN KEY (`xh`) REFERENCES `xs` (`xh`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `xk_ibfk_2` FOREIGN KEY (`kcbh`) REFERENCES `kc` (`kcbh`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xk`
--

LOCK TABLES `xk` WRITE;
/*!40000 ALTER TABLE `xk` DISABLE KEYS */;
INSERT INTO `xk` VALUES ('0324101','a101',78),('0324101','a104',63),('0324101','b110',90),('0324103','a101',88),('0324103','a104',88),('0324103','b110',85),('0324108','a101',90),('0324108','a104',72),('0324108','b110',NULL),('0324122','b110',NULL),('0341130','b110',NULL),('0341130','c022',66),('0341131','a101',84),('0341131','b110',NULL),('0341131','c022',88),('0424110','b110',NULL);
/*!40000 ALTER TABLE `xk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `xs`
--

DROP TABLE IF EXISTS `xs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `xs` (
  `xh` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `xm` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `xb` tinyint(1) DEFAULT NULL,
  `csrq` date DEFAULT NULL,
  `loan_price` float DEFAULT NULL COMMENT '贷款金额',
  `average_grade` float DEFAULT '0' COMMENT '平均成绩',
  PRIMARY KEY (`xh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `xs`
--

LOCK TABLES `xs` WRITE;
/*!40000 ALTER TABLE `xs` DISABLE KEYS */;
INSERT INTO `xs` VALUES ('0324101','李华',-1,'1984-02-05',1500,0),('0324103','冯海玲',0,'1983-09-19',1500,0),('0324108','杨平',-1,'1984-05-20',1500,0),('0324122','周强',-1,'1984-06-12',2000,0),('0341130','吴金发',-1,'1983-10-09',0,0),('0341131','陈红',0,'1986-05-09',0,0),('0424110','张锦康',-1,'1982-10-10',2000,0),('0442121','冯刚',-1,'1984-01-19',1000,0);
/*!40000 ALTER TABLE `xs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `xs_view`
--

DROP TABLE IF EXISTS `xs_view`;
/*!50001 DROP VIEW IF EXISTS `xs_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `xs_view` AS SELECT 
 1 AS `xh`,
 1 AS `xm`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `xs_view`
--

/*!50001 DROP VIEW IF EXISTS `xs_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=MERGE */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `xs_view` AS select `xs`.`xh` AS `xh`,`xs`.`xm` AS `xm` from `xs` where (`xs`.`xm` is not null) */
/*!50002 WITH CASCADED CHECK OPTION */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-17 14:32:49

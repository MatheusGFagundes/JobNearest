-- MySQL dump 10.13  Distrib 5.7.24, for Linux (x86_64)
--
-- Host: localhost    Database: simulatorjob
-- ------------------------------------------------------
-- Server version	5.7.24-0ubuntu0.18.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tb_jobs`
--

DROP TABLE IF EXISTS `tb_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `long` double DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `tb_simulator_id` int(11) NOT NULL,
  `education` int(11) NOT NULL,
  `salary_range` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tb_jobs_tb_simulator1` (`tb_simulator_id`),
  CONSTRAINT `fk_tb_jobs_tb_simulator1` FOREIGN KEY (`tb_simulator_id`) REFERENCES `tb_simulator` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=29899 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_residents`
--

DROP TABLE IF EXISTS `tb_residents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_residents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tb_simulator_id` int(11) NOT NULL,
  `lat` double DEFAULT NULL,
  `long` double DEFAULT NULL,
  `education` int(11) NOT NULL,
  `salary_range` int(11) NOT NULL,
  `transport` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tb_residents_tb_simulator` (`tb_simulator_id`),
  CONSTRAINT `fk_tb_residents_tb_simulator` FOREIGN KEY (`tb_simulator_id`) REFERENCES `tb_simulator` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=24050 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_residents_has_tb_jobs`
--

DROP TABLE IF EXISTS `tb_residents_has_tb_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_residents_has_tb_jobs` (
  `tb_residents_id` int(11) NOT NULL,
  `tb_jobs_id` int(11) NOT NULL,
  `simulator_id` int(11) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `match` tinyint(4) DEFAULT NULL,
  `distance` float DEFAULT NULL,
  `route_time` int(11) DEFAULT NULL,
  `mode` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`tb_residents_id`,`tb_jobs_id`),
  KEY `fk_tb_residents_has_tb_jobs_tb_jobs1` (`tb_jobs_id`),
  CONSTRAINT `fk_tb_residents_has_tb_jobs_tb_jobs1` FOREIGN KEY (`tb_jobs_id`) REFERENCES `tb_jobs` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_tb_residents_has_tb_jobs_tb_residents1` FOREIGN KEY (`tb_residents_id`) REFERENCES `tb_residents` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tb_simulator`
--

DROP TABLE IF EXISTS `tb_simulator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_simulator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `residents_count` int(11) DEFAULT NULL,
  `jobs_count` int(11) DEFAULT NULL,
  `distance_mean` double DEFAULT NULL,
  `time_routes_mean` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=188 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `teste`
--

DROP TABLE IF EXISTS `teste`;
/*!50001 DROP VIEW IF EXISTS `teste`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `teste` AS SELECT 
 1 AS `distance`,
 1 AS `j_id`,
 1 AS `j_education`,
 1 AS `j_salary_range`,
 1 AS `r_id`,
 1 AS `r_education`,
 1 AS `r_salary_range`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `teste`
--

/*!50001 DROP VIEW IF EXISTS `teste`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`user`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `teste` AS select (6353 * acos((((cos(radians(`job`.`lat`)) * cos(radians(`r`.`lat`))) * cos((radians(`r`.`long`) - radians(`job`.`long`)))) + (sin(radians(`job`.`lat`)) * sin(radians(`r`.`lat`)))))) AS `distance`,`job`.`id` AS `j_id`,`job`.`education` AS `j_education`,`job`.`salary_range` AS `j_salary_range`,`r`.`id` AS `r_id`,`r`.`education` AS `r_education`,`r`.`salary_range` AS `r_salary_range` from (`tb_jobs` `job` join `tb_residents` `r`) where ((`r`.`tb_simulator_id` = 156) and (`job`.`tb_simulator_id` = 156)) */;
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

-- Dump completed on 2018-11-25 14:26:31

-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: scramble_word
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
-- Table structure for table `scores`
--

DROP TABLE IF EXISTS `scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `player_name` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `score` int NOT NULL,
  `category` varchar(255) NOT NULL,
  `played_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scores`
--

LOCK TABLES `scores` WRITE;
/*!40000 ALTER TABLE `scores` DISABLE KEYS */;
INSERT INTO `scores` VALUES (1,'MARK','',3,'Animals','2024-11-29 05:15:28'),(2,'mark','',7,'Animals','2024-11-29 05:18:15'),(3,'mark','',3,'Animals','2024-11-29 07:29:26'),(4,'pael','',3,'Animals','2024-11-29 07:36:17'),(5,'kaka','',6,'Countries','2024-11-29 08:04:31'),(6,'mark','',10,'Animals','2024-11-30 13:39:08'),(7,'mark','',6,'Animals','2024-12-02 02:50:49'),(8,'mark','',10,'Animals','2024-12-02 13:20:48'),(9,'mark','',11,'Animals','2024-12-02 13:23:25'),(10,'karen','',2,'Animals','2024-12-02 13:24:18'),(11,'karen','',2,'Animals','2024-12-02 13:25:54'),(12,'mark','',9,'Fruits','2024-12-02 13:28:16'),(13,'MARK','',10,'Animals','2024-12-02 13:39:07'),(14,'MARK','',11,'Animals','2024-12-02 13:41:50'),(15,'MARK','',12,'Animals','2024-12-02 13:48:42'),(16,'MARK','',13,'Animals','2024-12-02 13:52:01'),(17,'mark','',3,'Countries','2024-12-02 13:53:24'),(18,'alilano','',7,'Sports','2024-12-02 14:00:33'),(19,'alilano','',7,'Sports','2024-12-02 14:07:59'),(20,'alilano','',7,'Sports','2024-12-02 14:09:25'),(21,'alilano','',7,'Sports','2024-12-02 14:10:11'),(22,'alilano','',7,'Sports','2024-12-02 14:10:28'),(23,'alilano','',7,'Sports','2024-12-02 14:11:12'),(24,'mark','',1,'Sports','2024-12-02 14:11:48'),(25,'mark','',1,'Sports','2024-12-02 14:13:11'),(26,'Mark Joseph','',10,'Sports','2024-12-02 14:14:33'),(27,'MIMI','',5,'Body Parts','2024-12-02 14:16:30'),(28,'MARKII','',10,'Body Parts','2024-12-02 14:18:09'),(29,'MARK','',2,'Fruits','2024-12-02 14:51:58'),(30,'MARK','',2,'Fruits','2024-12-02 14:53:22'),(31,'MARK','',2,'Fruits','2024-12-02 14:54:38'),(32,'MARK','',2,'Fruits','2024-12-02 14:54:59'),(33,'MARK','',2,'Fruits','2024-12-02 14:55:29'),(34,'MARK','',2,'Fruits','2024-12-02 14:56:11'),(35,'MARK','',2,'Fruits','2024-12-02 14:56:32'),(36,'MARK','',2,'Fruits','2024-12-02 14:56:54'),(37,'MARK','',2,'Fruits','2024-12-02 14:57:12'),(38,'MARKII','',5,'Clothing','2024-12-02 14:59:01'),(39,'MARKII','',2,'Colors','2024-12-03 01:23:38'),(40,'KARENN','',4,'Colors','2024-12-03 01:25:03'),(41,'PAEEL','',5,'Colors','2024-12-03 01:26:43'),(42,'MARKII','',0,'Animals','2024-12-03 01:48:21'),(43,'KRAMM','',0,'Technology','2024-12-03 01:49:26'),(44,'PALE','',0,'Fruits','2024-12-03 01:50:31');
/*!40000 ALTER TABLE `scores` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-12-03 22:25:36

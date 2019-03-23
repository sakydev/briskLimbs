-- MySQL dump 10.16  Distrib 10.1.37-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: limbs
-- ------------------------------------------------------
-- Server version	10.1.37-MariaDB

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
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'active_theme','ivar'),(2,'core_directory','core_directory'),(3,'core_url','core_url'),(4,'admin_theme','default'),(5,'title','Limbs'),(6,'title_separator','-'),(7,'description','A video sharing script built with both developers and managers in mind'),(9,'uploads','yes'),(10,'signups','yes'),(11,'public_message',''),(12,'upload_message','Please make sure you own the rights to upload this video'),(13,'comments','yes'),(14,'embeds','yes'),(15,'php','/usr/bin/php'),(16,'ffmpeg','/usr/bin/ffmpeg'),(17,'ffprobe','/usr/bin/ffprobe'),(18,'fresh','12'),(19,'trending','4'),(20,'search','5'),(21,'related','8'),(22,'quality_240','no'),(23,'quality_360','yes'),(24,'quality_480','no'),(25,'quality_720','yes'),(26,'quality_1080','no'),(27,'ffmpeg_preset','medium'),(28,'video_codec','libx264'),(29,'audio_codec','libfdk_aac'),(30,'basic_vbitrate','576'),(31,'basic_abitrate','64'),(32,'normal_vbitrate','896'),(33,'normal_abitrate','64'),(34,'sd_vbitrate','1536'),(35,'sd_abitrate','96'),(36,'hd_vbitrate','3072'),(37,'hd_abitrate','96'),(38,'fullhd_vbitrate','4992'),(39,'fullhd_abitrate','128'),(40,'watermark_placement','center:bottom');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-03-23 18:46:32

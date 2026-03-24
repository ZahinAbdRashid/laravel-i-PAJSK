-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: localhost    Database: i-pajsk
-- ------------------------------------------------------
-- Server version	9.4.0

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
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `type` enum('uniform','club','sport','competition','extra') COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` enum('school','district','state','national','international') COLLATE utf8mb4_unicode_ci NOT NULL,
  `achievement` enum('participation','third','second','first') COLLATE utf8mb4_unicode_ci NOT NULL,
  `activity_date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `teacher_comment` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activities_student_id_foreign` (`student_id`),
  KEY `activities_approved_by_foreign` (`approved_by`),
  CONSTRAINT `activities_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `teachers` (`id`),
  CONSTRAINT `activities_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activities`
--

LOCK TABLES `activities` WRITE;
/*!40000 ALTER TABLE `activities` DISABLE KEYS */;
INSERT INTO `activities` VALUES (14,5,'sport','mlbb','international','first','2025-12-28',NULL,'approved',NULL,7,'2025-12-28 07:42:04','2025-12-28 07:41:23','2025-12-28 07:42:04'),(15,5,'uniform','Camping','international','participation','2025-12-31',NULL,'rejected','test reject',7,'2026-02-04 17:28:45','2025-12-30 18:41:51','2026-02-04 17:28:45'),(16,5,'extra','Gotong royong surau','district','participation','2026-01-08',NULL,'approved','test reject',7,'2026-02-04 17:28:29','2026-01-08 00:42:52','2026-02-04 17:28:29');
/*!40000 ALTER TABLE `activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` bigint unsigned NOT NULL,
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_activity_id_foreign` (`activity_id`),
  CONSTRAINT `documents_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` VALUES (1,14,'XXni6Ij2DsgFpmPKumJ16jReKQzGa6HqYOgBCb42.pdf','BX3CL1FEF2AGE_booking.pdf','documents/activities/XXni6Ij2DsgFpmPKumJ16jReKQzGa6HqYOgBCb42.pdf','application/pdf',43601,'2025-12-28 07:41:23','2025-12-28 07:41:23'),(2,15,'1TZ3Gp9U4XrlWzb2DwWjs8yjmASFV1VX5IJH7GfL.jpg','IMG-20251230-WA0009.jpg','documents/activities/1TZ3Gp9U4XrlWzb2DwWjs8yjmASFV1VX5IJH7GfL.jpg','image/jpeg',73400,'2025-12-30 18:41:51','2025-12-30 18:41:51'),(3,16,'Iky4cfoepPgnPxFXgYzOgr3srXqpUU3zqFMU9ghD.jpg','IMG-20260108-WA0003.jpg','documents/activities/Iky4cfoepPgnPxFXgYzOgr3srXqpUU3zqFMU9ghD.jpg','image/jpeg',166609,'2026-01-08 00:42:52','2026-01-08 00:42:52');
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marks`
--

DROP TABLE IF EXISTS `marks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `uniform` int NOT NULL DEFAULT '0',
  `club` int NOT NULL DEFAULT '0',
  `sport` int NOT NULL DEFAULT '0',
  `competition` int NOT NULL DEFAULT '0',
  `extra` int NOT NULL DEFAULT '0',
  `total` int NOT NULL DEFAULT '0',
  `grade` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'E',
  `is_manual_override` tinyint(1) NOT NULL DEFAULT '0',
  `last_updated_by` bigint unsigned DEFAULT NULL,
  `override_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marks_student_id_unique` (`student_id`),
  KEY `marks_last_updated_by_foreign` (`last_updated_by`),
  CONSTRAINT `marks_last_updated_by_foreign` FOREIGN KEY (`last_updated_by`) REFERENCES `teachers` (`id`),
  CONSTRAINT `marks_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marks`
--

LOCK TABLES `marks` WRITE;
/*!40000 ALTER TABLE `marks` DISABLE KEYS */;
INSERT INTO `marks` VALUES (2,5,0,0,2,0,0,2,'E',1,7,NULL,'2025-12-28 07:42:04','2026-03-03 07:33:55');
/*!40000 ALTER TABLE `marks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2024_01_01_000001_create_users_table',1),(2,'2024_01_01_000002_create_teachers_table',1),(3,'2024_01_01_000003_create_students_table',1),(4,'2024_01_01_000004_create_activities_table',1),(5,'2024_01_01_000005_create_submissions_table',1),(6,'2024_01_01_000006_create_marks_table',1),(7,'2024_01_01_000007_create_suggestion_rules_table',1),(8,'2024_01_01_000008_create_documents_table',1),(10,'2025_12_22_164328_create_cache_table',2),(11,'2025_12_26_030405_remove_email_from_students_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned NOT NULL,
  `academic_session` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` enum('1','2','3') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sports` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `club` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uniform` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `students_user_id_foreign` (`user_id`),
  KEY `students_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `students_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (5,13,7,'2024/2025','1','athletics','ict','kadet polis','penolong ketua kelas','2025-12-28 07:40:07','2025-12-28 07:40:07',NULL),(6,14,7,'2024/2025','1','basketball','english','kadet bomba','ketua kelas','2025-12-29 04:01:22','2025-12-29 04:01:22',NULL),(7,15,7,'2024/2025','1','athletics','alam sekitar','kadet polis','pengawas sekolah','2025-12-29 04:02:08','2025-12-29 04:02:08',NULL);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `submissions`
--

DROP TABLE IF EXISTS `submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `teacher_feedback` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `submissions_activity_id_foreign` (`activity_id`),
  KEY `submissions_student_id_foreign` (`student_id`),
  KEY `submissions_reviewed_by_foreign` (`reviewed_by`),
  CONSTRAINT `submissions_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `submissions_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `teachers` (`id`),
  CONSTRAINT `submissions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `submissions`
--

LOCK TABLES `submissions` WRITE;
/*!40000 ALTER TABLE `submissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suggestion_rules`
--

DROP TABLE IF EXISTS `suggestion_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suggestion_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` int NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `conditions` json NOT NULL,
  `actions` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suggestion_rules`
--

LOCK TABLES `suggestion_rules` WRITE;
/*!40000 ALTER TABLE `suggestion_rules` DISABLE KEYS */;
INSERT INTO `suggestion_rules` VALUES (1,'Improve grade Sport and Grades',1,1,'[{\"type\": \"score\", \"value\": \"80\", \"operator\": \"less_than\"}, {\"type\": \"weak_component\", \"value\": \"sport\", \"operator\": \"equals\"}]','[{\"type\": \"suggest_activity\", \"level\": \"school\", \"points\": 5, \"message\": \"Untuk sesiapa yang ingin bermain MLBB di peringkat sekolah\", \"achievement\": \"participation\", \"activityName\": \"Tournament MLBB 2026\", \"activityType\": \"sport\"}]','2026-03-03 07:29:55','2026-03-03 07:29:55',NULL),(2,'Improve grade score',2,1,'[{\"type\": \"grade\", \"value\": \"C\", \"operator\": \"less_or_equal\"}]','[{\"type\": \"suggest_activity\", \"level\": \"school\", \"points\": 5, \"message\": \"Camping Gunung Ledang (27 March 2026)\", \"achievement\": \"participation\", \"activityName\": \"camping\", \"activityType\": \"uniform\"}, {\"type\": \"suggest_activity\", \"level\": \"district\", \"points\": 10, \"message\": \"Pertandingan Public Speaking Peringkat Daerah Alor Gajah (4 April 2026)\", \"achievement\": \"participation\", \"activityName\": \"Public Speaking\", \"activityType\": \"club\"}, {\"type\": \"suggest_activity\", \"level\": \"school\", \"points\": 5, \"message\": \"Bola Sepak peringkat Sekolah (8 Mei 2026)\", \"achievement\": \"participation\", \"activityName\": \"Bola Sepak\", \"activityType\": \"sport\"}, {\"type\": \"suggest_activity\", \"level\": \"state\", \"points\": 5, \"message\": \"Kempen Hari Sukan Negara Fun Run (7 March 2026)\", \"achievement\": \"participation\", \"activityName\": \"Hari Sukan Negara\", \"activityType\": \"competition\"}]','2026-03-03 13:57:19','2026-03-03 13:57:19',NULL);
/*!40000 ALTER TABLE `suggestion_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teachers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `staff_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assigned_class` enum('alpha','delta','omega') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `teachers_staff_id_unique` (`staff_id`),
  KEY `teachers_user_id_foreign` (`user_id`),
  CONSTRAINT `teachers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
INSERT INTO `teachers` VALUES (5,10,'STF00003','BAHASA MELAYU','alpha','2025-12-28 06:37:40','2025-12-28 07:22:47',NULL),(6,11,'STF00002','ENGLISH','delta','2025-12-28 07:22:29','2025-12-28 07:22:29',NULL),(7,12,'STF00001','SEJARAH','omega','2025-12-28 07:24:01','2025-12-28 07:24:01',NULL);
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ic_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','teacher','student') COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_ic_number_unique` (`ic_number`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'000000-00-0001','System Administrator','admin@ipajsk.edu.my','$2y$12$7.egp9vNn4Wl.bCCURtX/OSTQ9mJ98jTZTWc.zL5YBv.v52eLK/XG','admin','012-3456789','male',NULL,'2025-12-22 08:10:26','2025-12-22 08:10:26',NULL),(10,'850505-10-1234','ALI BIN ABU','ali@school.edu.my','$2y$12$6tRjncdfxE4aUYnnBgKZ4eQZs1Gitzmqnd8nShS91JcZWwIZEk3zG','teacher','012-3456789','male',NULL,'2025-12-28 06:37:40','2025-12-28 06:37:40',NULL),(11,'880808-08-8888','SITI KASIM BINTI ZUBIR','siti@teacher.edu.my','$2y$12$wwdGlyPk363i7RjGTD746eP.FboxWFUiagdRvK7kGJjJJN54vKiF6','teacher','012-3132424','female',NULL,'2025-12-28 07:22:28','2025-12-28 07:22:28',NULL),(12,'801212-14-5678','SIFULAN BIN SIFULAN','sifulan@teacher.edu.my','$2y$12$xngw.rD.YHCU1TsX1WRmB.VtXCD04omm8nnB2agQ/509s8BowHkny','teacher','012-4679242','male',NULL,'2025-12-28 07:24:01','2025-12-28 07:24:01',NULL),(13,'020314-01-0809','Muhammad Zahin Bin Abd Rashid','020314-01-0809@student.edu.my','$2y$12$BHUS4R2X6cdIBl5kwNDEcuZkPTHZ28w7PHDqU49nR7cwxYbcX1dc2','student',NULL,'male',NULL,'2025-12-28 07:40:07','2025-12-30 21:30:24',NULL),(14,'070101-01-0001','ALI BIN ABU','070101-01-0001@student.edu.my','$2y$12$Z2Kbp.vMtEzE2Pmy3xr9Se9lU.cp68mTTppcumFJexhigCQwuSW6a','student',NULL,'male',NULL,'2025-12-29 04:01:22','2025-12-29 04:01:22',NULL),(15,'070101-01-0002','NUR UMAIRAH BINTI SHAH','070101-01-0002@student.edu.my','$2y$12$zoaBdQYcaXb5CSEsJmAU8.oJKw2qPhtzFfKDUuERvQJvYxzOr/oHi','student',NULL,'male',NULL,'2025-12-29 04:02:08','2025-12-29 04:02:08',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-04 14:28:29

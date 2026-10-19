/*
SQLyog Community v13.3.1 (64 bit)
MySQL - 10.4.32-MariaDB : Database - shalon
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`shalon` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `shalon`;

/*Table structure for table `bookings` */

DROP TABLE IF EXISTS `bookings`;

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('Pending','Confirmed','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_booking_user` (`user_id`),
  KEY `fk_booking_service` (`service_id`),
  CONSTRAINT `fk_booking_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `bookings` */

/*Table structure for table `products` */

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `products` */

insert  into `products`(`id`,`name`,`description`,`price`,`stock`,`created_at`) values 
(1,'Rose Glow Shampoo','Gentle shampoo for everyday hair care.',280.00,20,'2026-10-19 21:16:26'),
(2,'Silky Hair Serum','A smoothing serum for a glossy finish.',350.00,15,'2026-10-19 21:16:26'),
(3,'Pink Petal Hand Cream','Moisturizing cream for soft hands.',180.00,25,'2026-10-19 21:16:26'),
(4,'Hydrating Face Mask','A hydrating mask for a refreshed feel.',220.00,18,'2026-10-19 21:16:26'),
(5,'Nourishing Hair Mask','Deep-conditioning hair care treatment.',400.00,12,'2026-10-19 21:16:26'),
(6,'Rose Lip Balm','A moisturizing lip balm with a rosy finish.',120.00,30,'2026-10-19 21:16:26');

/*Table structure for table `services` */

DROP TABLE IF EXISTS `services`;

CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `duration` int(11) NOT NULL DEFAULT 30,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `services` */

insert  into `services`(`id`,`name`,`description`,`price`,`duration`,`created_at`) values 
(1,'Haircut & Styling','A fresh haircut with professional styling.',350.00,45,'2026-10-19 21:16:13'),
(2,'Manicure','Nail shaping, cuticle care, and polish.',250.00,30,'2026-10-19 21:16:13'),
(3,'Pedicure','Relaxing foot care and nail treatment.',350.00,45,'2026-10-19 21:16:13'),
(4,'Basic Facial','A refreshing facial cleansing treatment.',500.00,60,'2026-10-19 21:16:13'),
(5,'Hair Coloring','Professional hair coloring consultation and service.',1200.00,120,'2026-10-19 21:16:13'),
(6,'Hair Treatment','Nourishing treatment for soft, healthy-looking hair.',650.00,60,'2026-10-19 21:16:13'),
(7,'Eyelash Extensions','Enhance your look with beautiful lashes.',800.00,90,'2026-10-19 21:16:13'),
(8,'Gel Nail Polish','Long-lasting, glossy gel nail color.',450.00,45,'2026-10-19 21:16:13');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `users` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

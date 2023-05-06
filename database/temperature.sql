-- MySQL Script generated by MySQL Workbench
-- Sat May  6 18:32:41 2023
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema temperature
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema temperature
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `temperature` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;
USE `temperature` ;

-- -----------------------------------------------------
-- Table `temperature`.`temperature`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `temperature`.`temperature` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `date_time` DATETIME NULL,
  `cpu` DECIMAL(6,3) NULL,
  `gpu` DECIMAL(6,3) NULL,
  `temperature` DECIMAL(6,3) NULL,
  `sensation` DECIMAL(6,3) NULL,
  `wind_direction` VARCHAR(6) NULL,
  `wind_velocity` DECIMAL(6,3) NULL,
  `humidity` DECIMAL(7,3) NULL,
  `weather_condition` VARCHAR(60) NULL,
  `pressure` INT UNSIGNED NULL,
  `icon` VARCHAR(2) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `datetime_INDEX` (`date_time` ASC))
ENGINE = InnoDB;

CREATE USER 'temperature' IDENTIFIED BY 'temperature.68';

GRANT ALL ON `temperature`.* TO 'temperature';
GRANT SELECT ON TABLE `temperature`.* TO 'temperature';
GRANT SELECT, INSERT, TRIGGER, UPDATE, DELETE ON TABLE `temperature`.* TO 'temperature';
GRANT SELECT, INSERT, TRIGGER ON TABLE `temperature`.* TO 'temperature';
GRANT EXECUTE ON ROUTINE `temperature`.* TO 'temperature';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

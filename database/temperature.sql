CREATE TABLE IF NOT EXISTS `temperature`.`city` (
  `id` INT NOT NULL,
  `name` VARCHAR(60) NOT NULL,
  `state` VARCHAR(2) NOT NULL,
  `country` VARCHAR(2) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL,
  `selected` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `country_state_INDEX` (`country` ASC, `state` ASC) VISIBLE,
  INDEX `selected_INDEX` (`selected` ASC) VISIBLE)
ENGINE = InnoDB;


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
  `icon` VARCHAR(3) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL,
  `city_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `datetime_INDEX` (`date_time` ASC) VISIBLE,
  INDEX `fk_temperature_city_idx` (`city_id` ASC) VISIBLE,
  CONSTRAINT `fk_temperature_city`
    FOREIGN KEY (`city_id`)
    REFERENCES `temperature`.`city` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `temperature`.`configuration` (
  `id` BINARY(16) NOT NULL,
  `param_name` VARCHAR(255) NOT NULL,
  `param_value` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `param_name_UNIQUE` (`param_name` ASC) VISIBLE)
ENGINE = InnoDB;
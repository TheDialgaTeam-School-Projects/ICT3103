-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema teamwork
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `teamwork` ;

-- -----------------------------------------------------
-- Schema teamwork
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `teamwork` DEFAULT CHARACTER SET utf8 ;
USE `teamwork` ;

-- -----------------------------------------------------
-- Table `teamwork`.`user_account`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `teamwork`.`user_account` ;

CREATE TABLE IF NOT EXISTS `teamwork`.`user_account` (
  `username` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone` INT NOT NULL,
  `first_name` VARCHAR(255) NOT NULL,
  `last_name` VARCHAR(255) NOT NULL,
  `date_of_birth` DATE NOT NULL,
  PRIMARY KEY (`username`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `teamwork`.`logger`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `teamwork`.`logger` ;

CREATE TABLE IF NOT EXISTS `teamwork`.`logger` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `logged_datetime` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_logger_user_account_idx` (`username` ASC) VISIBLE,
  CONSTRAINT `fk_logger_user_account`
    FOREIGN KEY (`username`)
    REFERENCES `teamwork`.`user_account` (`username`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

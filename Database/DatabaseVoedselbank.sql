-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`Gebruikers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Gebruikers` (
  `idGebruikers` INT NOT NULL AUTO_INCREMENT,
  `Gebruikersnaam` VARCHAR(45) NOT NULL,
  `Wachtwoord` VARCHAR(255) NOT NULL,
  `Rol` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idGebruikers`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Klanten`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Klanten` (
  `idKlanten` INT NOT NULL AUTO_INCREMENT,
  `naam` VARCHAR(45) NOT NULL,
  `adres` VARCHAR(45) NOT NULL,
  `telefoonnummer` INT NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `aantal_volwassenen` INT NOT NULL,
  `aantal_kinderen` INT NOT NULL,
  `aantal_babys` INT NOT NULL,
  PRIMARY KEY (`idKlanten`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Voedselpakketen`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Voedselpakketen` (
  `idVoedselpakketen` INT NOT NULL AUTO_INCREMENT,
  `Klant_id` VARCHAR(45) NOT NULL,
  `Gebruiker_id` VARCHAR(45) NOT NULL,
  `Samenstellingsdatum` DATE NOT NULL,
  `Uitgiftedatum` DATE NOT NULL,
  `Klanten_idKlanten` INT NOT NULL,
  PRIMARY KEY (`idVoedselpakketen`, `Klanten_idKlanten`),
  INDEX `fk_Voedselpakketen_Klanten_idx` (`Klanten_idKlanten` ASC) VISIBLE,
  CONSTRAINT `fk_Voedselpakketen_Klanten`
    FOREIGN KEY (`Klanten_idKlanten`)
    REFERENCES `mydb`.`Klanten` (`idKlanten`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Categorieen`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Categorieen` (
  `idCategorieen` INT NOT NULL AUTO_INCREMENT,
  `naam` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idCategorieen`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Producten`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Producten` (
  `idProducten` INT NOT NULL AUTO_INCREMENT,
  `naam` VARCHAR(45) NOT NULL,
  `categorie_id` VARCHAR(45) NOT NULL,
  `ean` INT NOT NULL,
  `aantal` INT NOT NULL,
  `leverancier_id` VARCHAR(45) NOT NULL,
  `Categorieen_idCategorieen` INT NOT NULL,
  PRIMARY KEY (`idProducten`, `Categorieen_idCategorieen`),
  INDEX `fk_Producten_Categorieen1_idx` (`Categorieen_idCategorieen` ASC) VISIBLE,
  CONSTRAINT `fk_Producten_Categorieen1`
    FOREIGN KEY (`Categorieen_idCategorieen`)
    REFERENCES `mydb`.`Categorieen` (`idCategorieen`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Leveranciers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Leveranciers` (
  `idLeveranciers` INT NOT NULL AUTO_INCREMENT,
  `naam` VARCHAR(45) NOT NULL,
  `contactpersoon` VARCHAR(45) NOT NULL,
  `telefoonnummer` INT NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `eerstevolgende_levering` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idLeveranciers`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Dieetwensen`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Dieetwensen` (
  `idDieetwensen` INT NOT NULL AUTO_INCREMENT,
  `naam` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idDieetwensen`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Producten_has_Leveranciers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Producten_has_Leveranciers` (
  `Producten_idProducten` INT NOT NULL,
  `Leveranciers_idLeveranciers` INT NOT NULL,
  PRIMARY KEY (`Producten_idProducten`, `Leveranciers_idLeveranciers`),
  INDEX `fk_Producten_has_Leveranciers_Leveranciers1_idx` (`Leveranciers_idLeveranciers` ASC) VISIBLE,
  INDEX `fk_Producten_has_Leveranciers_Producten1_idx` (`Producten_idProducten` ASC) VISIBLE,
  CONSTRAINT `fk_Producten_has_Leveranciers_Producten1`
    FOREIGN KEY (`Producten_idProducten`)
    REFERENCES `mydb`.`Producten` (`idProducten`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Producten_has_Leveranciers_Leveranciers1`
    FOREIGN KEY (`Leveranciers_idLeveranciers`)
    REFERENCES `mydb`.`Leveranciers` (`idLeveranciers`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Klanten_has_Dieetwensen`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Klanten_has_Dieetwensen` (
  `Klanten_idKlanten` INT NOT NULL,
  `Dieetwensen_idDieetwensen` INT NOT NULL,
  PRIMARY KEY (`Klanten_idKlanten`, `Dieetwensen_idDieetwensen`),
  INDEX `fk_Klanten_has_Dieetwensen_Dieetwensen1_idx` (`Dieetwensen_idDieetwensen` ASC) VISIBLE,
  INDEX `fk_Klanten_has_Dieetwensen_Klanten1_idx` (`Klanten_idKlanten` ASC) VISIBLE,
  CONSTRAINT `fk_Klanten_has_Dieetwensen_Klanten1`
    FOREIGN KEY (`Klanten_idKlanten`)
    REFERENCES `mydb`.`Klanten` (`idKlanten`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Klanten_has_Dieetwensen_Dieetwensen1`
    FOREIGN KEY (`Dieetwensen_idDieetwensen`)
    REFERENCES `mydb`.`Dieetwensen` (`idDieetwensen`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Producten_has_Voedselpakketen`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Producten_has_Voedselpakketen` (
  `Producten_idProducten` INT NOT NULL,
  `Producten_Categorieen_idCategorieen` INT NOT NULL,
  `Voedselpakketen_idVoedselpakketen` INT NOT NULL,
  `Voedselpakketen_Klanten_idKlanten` INT NOT NULL,
  PRIMARY KEY (`Producten_idProducten`, `Producten_Categorieen_idCategorieen`, `Voedselpakketen_idVoedselpakketen`, `Voedselpakketen_Klanten_idKlanten`),
  INDEX `fk_Producten_has_Voedselpakketen_Voedselpakketen1_idx` (`Voedselpakketen_idVoedselpakketen` ASC, `Voedselpakketen_Klanten_idKlanten` ASC) VISIBLE,
  INDEX `fk_Producten_has_Voedselpakketen_Producten1_idx` (`Producten_idProducten` ASC, `Producten_Categorieen_idCategorieen` ASC) VISIBLE,
  CONSTRAINT `fk_Producten_has_Voedselpakketen_Producten1`
    FOREIGN KEY (`Producten_idProducten` , `Producten_Categorieen_idCategorieen`)
    REFERENCES `mydb`.`Producten` (`idProducten` , `Categorieen_idCategorieen`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Producten_has_Voedselpakketen_Voedselpakketen1`
    FOREIGN KEY (`Voedselpakketen_idVoedselpakketen` , `Voedselpakketen_Klanten_idKlanten`)
    REFERENCES `mydb`.`Voedselpakketen` (`idVoedselpakketen` , `Klanten_idKlanten`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

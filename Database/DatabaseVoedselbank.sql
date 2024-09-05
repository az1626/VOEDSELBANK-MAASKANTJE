-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 15 jun 2023 om 15:10
-- Serverversie: 10.4.25-MariaDB
-- PHP-versie: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: voedselbank_db
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel extra
--

CREATE TABLE extra (
  beschikbare_allergieën varchar(255) NOT NULL,
  beschikbare_categorieën varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel extra
--

INSERT INTO extra (beschikbare_allergieën, beschikbare_categorieën) VALUES
('Melk(eiwit) allergie', 'Pasta, Rijst en wereldkeuken'),
('Niks', 'Aardappelen, Groente, Fruit'),
('Notenallergie', 'Zuivel, Plantaardig en eieren'),
('Pindaallergie', 'Bakkerij en Banket'),
('Sesamallergie', 'Frisdrank, Sappen, Kofie en Thee'),
('Snelle bezorging', 'Baby, Verzorging, Hygiene'),
('Sojaallergie', 'Soepen, Sauzen, Kruiden en Olie'),
('Tarweallergie', 'Kaas, Vleeswaren'),
('Visallergie', 'Snoep, Koek, Chips en Chocolade');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel gezinnen
--

CREATE TABLE gezinnen (
  id int(11) NOT NULL,
  naam varchar(200) NOT NULL,
  volwassenen int(11) NOT NULL,
  kinderen int(11) NOT NULL,
  postcode varchar(25) NOT NULL,
  mail varchar(255) NOT NULL,
  telefoonnummer int(11) NOT NULL,
  wensen varchar(255) NOT NULL,
  pakket varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel gezinnen
--

INSERT INTO gezinnen (id, naam, volwassenen, kinderen, postcode, mail, telefoonnummer, wensen, pakket) VALUES
(48, 'peiters', 2, 2, '1333bn', 'mongus@gmail.com', 2147483647, 'notenallergie, niks', ''),
(61, 'kaas', 2, 3, '1441LO', 'kaas@gmail.com', 682836458, 'notenallergie, sesamallergie, tarweallergie', '');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel leveranciers
--

CREATE TABLE leveranciers (
  id int(11) NOT NULL,
  naam varchar(255) NOT NULL,
  mail varchar(100) NOT NULL,
  telefoonnummer int(11) NOT NULL,
  postcode varchar(6) NOT NULL,
  bezorgingsdatum date NOT NULL,
  bezorgingstijd varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel leveranciers
--

INSERT INTO leveranciers (id, naam, mail, telefoonnummer, postcode, bezorgingsdatum, bezorgingstijd) VALUES
(12, 'postnl', 'postnl@nl', 640273918, '1444LO', '2023-06-14', '21:10'),
(15, 'DHL', 'DHL@DHL.nl', 123456789, '1333ZB', '2023-06-09', '15:57');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel producten
--

CREATE TABLE producten (
  id int(11) NOT NULL,
  naam varchar(255) NOT NULL,
  beschrijving varchar(100) NOT NULL,
  categorie varchar(500) NOT NULL,
  voorraad int(11) NOT NULL,
  EAN_Nummer varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel producten
--

INSERT INTO producten (id, naam, beschrijving, categorie, voorraad, EAN_Nummer) VALUES
(29, 'kaas', 'kaas', 'Bakkerij en Banket', 54, '11267532348'),
(30, 'melk', 'melk', 'Snoep, Koek, Chips en Chocolade', 149, '12345678910'),
(31, 'groene melk', 'de melk is groen', 'Sappen', 59, '29343232457324732748');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel user
--

CREATE TABLE user (
  AccountID int(11) NOT NULL,
  Email varchar(100) NOT NULL,
  Wachtwoord varchar(255) NOT NULL,
  Naam varchar(100) NOT NULL,
  Telefoonnummer varchar(100) NOT NULL,
  role int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel user
--

INSERT INTO user (AccountID, Email, Wachtwoord, Naam, Telefoonnummer, role) VALUES
(1, 'oguzhanarguden@gmail.com', '$2y$10$5i1nXjle256QS6J.uFJAcur5HNx6yeLnBLym0IrFiABLXmKPf/H6a', 'Oguzhan', '0612345678', 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel voedselpakket
--

CREATE TABLE voedselpakket (
  id int(11) NOT NULL,
  naam varchar(255) NOT NULL,
  producten varchar(255) NOT NULL,
  samenstellingsdatum date NOT NULL DEFAULT '0000-00-00',
  ophaaldatum date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Gegevens worden geëxporteerd voor tabel voedselpakket
--

INSERT INTO voedselpakket (id, naam, producten, samenstellingsdatum, ophaaldatum) VALUES
(11, 'Familiepakket', 'kaas x3, melk x3, groene melk x4', '2023-06-13', '2023-06-22'),
(12, 'yes', 'kaas x1, melk x2, groene melk x24', '2023-06-20', '2023-06-30'),
(13, 'ik hou van melk', 'groene melk x3, kaas x1, melk x5', '2023-06-21', '2023-06-23'),
(14, 'ik hou van melkll', 'groene melk x3, kaas x4, melk x5', '2023-06-21', '2023-06-24'),
(15, 'ik hou van eten', 'groene melk x2, kaas x3, melk x4', '2023-06-20', '2023-06-25'),
(16, 'pakket ', 'groene melk x4, kaas x4, melk x4', '2023-06-16', '2023-06-17'),
(19, 'pakket ', 'groene melk x3, kaas x3, melk x3', '2023-06-20', '2023-06-23'),
(20, 'pakket ', 'groene melk x3, kaas x3, melk x3', '2023-06-20', '2023-06-23'),
(21, 'pakket ', 'groene melk x4, kaas x4, melk x4', '2023-06-22', '2023-06-23'),
(22, 'pakket ', 'groene melk x4, kaas x4, melk x4', '2023-06-22', '2023-06-23'),
(23, 'pakket ', 'groene melk x4, kaas x4, melk x4', '2023-06-19', '2023-06-23');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel extra
--
ALTER TABLE extra
  ADD PRIMARY KEY (beschikbare_allergieën);

--
-- Indexen voor tabel gezinnen
--
ALTER TABLE gezinnen
  ADD PRIMARY KEY (id);

--
-- Indexen voor tabel leveranciers
--
ALTER TABLE leveranciers
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY username (naam);

--
-- Indexen voor tabel producten
--
ALTER TABLE producten
  ADD PRIMARY KEY (id);

--
-- Indexen voor tabel user
--
ALTER TABLE user
  ADD PRIMARY KEY (AccountID);

--
-- Indexen voor tabel voedselpakket
--
ALTER TABLE voedselpakket
  ADD PRIMARY KEY (id);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel gezinnen
--
ALTER TABLE gezinnen
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT voor een tabel leveranciers
--
ALTER TABLE leveranciers
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT voor een tabel producten
--
ALTER TABLE producten
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT voor een tabel user
--
ALTER TABLE user
  MODIFY AccountID int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT voor een tabel voedselpakket
--
ALTER TABLE voedselpakket
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
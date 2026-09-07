-- Kinetika — šema baze, pogledi, procedure i početni katalog delova tela
-- MySQL 8, InnoDB, utf8mb4

CREATE DATABASE IF NOT EXISTS kinetika
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE kinetika;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP VIEW IF EXISTS v_pregled_toka_terapije;
DROP TABLE IF EXISTS placanje;
DROP TABLE IF EXISTS seansa;
DROP TABLE IF EXISTS plan_vezba;
DROP TABLE IF EXISTS vezba;
DROP TABLE IF EXISTS terapijski_plan;
DROP TABLE IF EXISTS povreda;
DROP TABLE IF EXISTS deo_tela;
DROP TABLE IF EXISTS pacijent;
DROP TABLE IF EXISTS fizioterapeut;
DROP TABLE IF EXISTS korisnik;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE korisnik (
  id_korisnika INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL,
  lozinka_hash VARCHAR(255) NOT NULL,
  uloga ENUM('admin', 'fizioterapeut', 'pacijent') NOT NULL,
  aktivan TINYINT(1) NOT NULL DEFAULT 1,
  datum_kreiranja DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_korisnik_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE fizioterapeut (
  id_fizioterapeuta INT AUTO_INCREMENT PRIMARY KEY,
  id_korisnika INT NOT NULL,
  ime VARCHAR(80) NOT NULL,
  prezime VARCHAR(80) NOT NULL,
  telefon VARCHAR(30) NULL,
  boja_kalendara VARCHAR(7) NOT NULL DEFAULT '#5F6F5B',
  UNIQUE KEY uq_ft_korisnik (id_korisnika),
  CONSTRAINT fk_ft_korisnik FOREIGN KEY (id_korisnika) REFERENCES korisnik (id_korisnika)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pacijent (
  id_pacijenta INT AUTO_INCREMENT PRIMARY KEY,
  id_korisnika INT NOT NULL,
  id_fizioterapeuta INT NOT NULL,
  ime VARCHAR(80) NOT NULL,
  prezime VARCHAR(80) NOT NULL,
  telefon VARCHAR(30) NULL,
  UNIQUE KEY uq_pac_korisnik (id_korisnika),
  KEY ix_pac_ft (id_fizioterapeuta),
  CONSTRAINT fk_pac_korisnik FOREIGN KEY (id_korisnika) REFERENCES korisnik (id_korisnika),
  CONSTRAINT fk_pac_ft FOREIGN KEY (id_fizioterapeuta) REFERENCES fizioterapeut (id_fizioterapeuta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE deo_tela (
  id_dela_tela INT AUTO_INCREMENT PRIMARY KEY,
  naziv VARCHAR(80) NOT NULL,
  grupa VARCHAR(40) NOT NULL,
  UNIQUE KEY uq_deo_tela_naziv (naziv)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE povreda (
  id_povrede INT AUTO_INCREMENT PRIMARY KEY,
  id_pacijenta INT NOT NULL,
  id_dela_tela INT NOT NULL,
  tip VARCHAR(120) NOT NULL,
  datum_povrede DATE NOT NULL,
  tezina ENUM('blaga', 'umerena', 'teska') NOT NULL DEFAULT 'umerena',
  opis TEXT NULL,
  KEY ix_pov_pac (id_pacijenta),
  CONSTRAINT fk_pov_pac FOREIGN KEY (id_pacijenta) REFERENCES pacijent (id_pacijenta),
  CONSTRAINT fk_pov_deo FOREIGN KEY (id_dela_tela) REFERENCES deo_tela (id_dela_tela)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE terapijski_plan (
  id_plana INT AUTO_INCREMENT PRIMARY KEY,
  id_povrede INT NOT NULL,
  id_pacijenta INT NOT NULL,
  id_fizioterapeuta INT NOT NULL,
  cilj VARCHAR(255) NOT NULL,
  datum_od DATE NOT NULL,
  datum_do DATE NULL,
  predvidjen_broj_seansi INT NOT NULL DEFAULT 8,
  status ENUM('nacrt', 'aktivan', 'zavrsen', 'prekinut') NOT NULL DEFAULT 'aktivan',
  nacin_naplate ENUM('po_planu', 'po_seansi', 'oba') NOT NULL DEFAULT 'po_seansi',
  cena DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  napomena TEXT NULL,
  KEY ix_plan_pac (id_pacijenta),
  KEY ix_plan_ft (id_fizioterapeuta),
  CONSTRAINT fk_plan_pov FOREIGN KEY (id_povrede) REFERENCES povreda (id_povrede),
  CONSTRAINT fk_plan_pac FOREIGN KEY (id_pacijenta) REFERENCES pacijent (id_pacijenta),
  CONSTRAINT fk_plan_ft FOREIGN KEY (id_fizioterapeuta) REFERENCES fizioterapeut (id_fizioterapeuta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vezba (
  id_vezbe INT AUTO_INCREMENT PRIMARY KEY,
  naziv VARCHAR(120) NOT NULL,
  grupa_misica VARCHAR(80) NOT NULL,
  opis TEXT NULL,
  kontraindikacije TEXT NULL,
  id_kreirao INT NOT NULL,
  aktivna TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_vezba_kreirao FOREIGN KEY (id_kreirao) REFERENCES korisnik (id_korisnika)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE plan_vezba (
  id_plan_vezba INT AUTO_INCREMENT PRIMARY KEY,
  id_plana INT NOT NULL,
  id_vezbe INT NOT NULL,
  id_dela_tela INT NOT NULL,
  serije TINYINT NOT NULL DEFAULT 3,
  ponavljanja TINYINT NOT NULL DEFAULT 10,
  napomena VARCHAR(255) NULL,
  CONSTRAINT fk_pv_plan FOREIGN KEY (id_plana) REFERENCES terapijski_plan (id_plana) ON DELETE CASCADE,
  CONSTRAINT fk_pv_vezba FOREIGN KEY (id_vezbe) REFERENCES vezba (id_vezbe),
  CONSTRAINT fk_pv_deo FOREIGN KEY (id_dela_tela) REFERENCES deo_tela (id_dela_tela)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE seansa (
  id_seanse INT AUTO_INCREMENT PRIMARY KEY,
  id_plana INT NOT NULL,
  id_pacijenta INT NOT NULL,
  id_fizioterapeuta INT NOT NULL,
  pocetak DATETIME NOT NULL,
  trajanje_min SMALLINT NOT NULL DEFAULT 60,
  status ENUM('zahtevana', 'zakazana', 'odrzana', 'odbijena', 'otkazana', 'propustena') NOT NULL DEFAULT 'zahtevana',
  nivo_bola TINYINT NULL,
  cena DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  napomena TEXT NULL,
  otkazao_uloga ENUM('admin', 'fizioterapeut', 'pacijent') NULL,
  KEY ix_seansa_slot (id_fizioterapeuta, pocetak, status),
  KEY ix_seansa_pac (id_pacijenta),
  CONSTRAINT fk_seansa_plan FOREIGN KEY (id_plana) REFERENCES terapijski_plan (id_plana),
  CONSTRAINT fk_seansa_pac FOREIGN KEY (id_pacijenta) REFERENCES pacijent (id_pacijenta),
  CONSTRAINT fk_seansa_ft FOREIGN KEY (id_fizioterapeuta) REFERENCES fizioterapeut (id_fizioterapeuta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE placanje (
  id_placanja INT AUTO_INCREMENT PRIMARY KEY,
  id_pacijenta INT NOT NULL,
  id_plana INT NULL,
  id_seanse INT NULL,
  tip ENUM('zaduzenje', 'uplata') NOT NULL,
  iznos DECIMAL(10, 2) NOT NULL,
  datum DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  nacin ENUM('gotovina', 'kartica', 'prenos') NULL,
  napomena VARCHAR(255) NULL,
  KEY ix_pl_pac (id_pacijenta),
  CONSTRAINT fk_pl_pac FOREIGN KEY (id_pacijenta) REFERENCES pacijent (id_pacijenta),
  CONSTRAINT fk_pl_plan FOREIGN KEY (id_plana) REFERENCES terapijski_plan (id_plana),
  CONSTRAINT fk_pl_seansa FOREIGN KEY (id_seanse) REFERENCES seansa (id_seanse)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE OR REPLACE VIEW v_pregled_toka_terapije AS
SELECT
  p.id_plana,
  pac.id_pacijenta,
  pac.ime AS ime_pacijenta,
  pac.prezime AS prezime_pacijenta,
  ft.id_fizioterapeuta,
  ft.ime AS ime_terapeuta,
  ft.prezime AS prezime_terapeuta,
  dt.naziv AS deo_tela,
  pov.tip AS tip_povrede,
  p.status AS status_plana,
  p.nacin_naplate,
  p.predvidjen_broj_seansi,
  (
    SELECT COUNT(*)
    FROM seansa s
    WHERE s.id_plana = p.id_plana AND s.status = 'odrzana'
  ) AS broj_odrzanih,
  (
    SELECT s.nivo_bola
    FROM seansa s
    WHERE s.id_plana = p.id_plana AND s.status = 'odrzana'
    ORDER BY s.pocetak DESC
    LIMIT 1
  ) AS poslednji_bol,
  (
    SELECT COALESCE(SUM(x.iznos), 0)
    FROM placanje x
    WHERE x.id_pacijenta = pac.id_pacijenta AND x.tip = 'zaduzenje'
  ) -
  (
    SELECT COALESCE(SUM(x.iznos), 0)
    FROM placanje x
    WHERE x.id_pacijenta = pac.id_pacijenta AND x.tip = 'uplata'
  ) AS dug
FROM terapijski_plan p
JOIN pacijent pac ON pac.id_pacijenta = p.id_pacijenta
JOIN fizioterapeut ft ON ft.id_fizioterapeuta = p.id_fizioterapeuta
JOIN povreda pov ON pov.id_povrede = p.id_povrede
JOIN deo_tela dt ON dt.id_dela_tela = pov.id_dela_tela;

DROP PROCEDURE IF EXISTS sp_potvrdi_zahtev_seanse;
DROP PROCEDURE IF EXISTS sp_evidentiraj_uplatu;

DELIMITER $$

CREATE PROCEDURE sp_potvrdi_zahtev_seanse(IN p_id_seanse INT)
BEGIN
  DECLARE v_status VARCHAR(20);
  DECLARE v_ft INT;
  DECLARE v_pocetak DATETIME;
  DECLARE v_trajanje INT;
  DECLARE v_preklapanje INT DEFAULT 0;

  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    RESIGNAL;
  END;

  START TRANSACTION;

  SELECT status, id_fizioterapeuta, pocetak, trajanje_min
    INTO v_status, v_ft, v_pocetak, v_trajanje
  FROM seansa
  WHERE id_seanse = p_id_seanse
  FOR UPDATE;

  IF v_status IS NULL THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Seansa ne postoji.';
  END IF;

  IF v_status <> 'zahtevana' THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Seansa nije u statusu zahtevana.';
  END IF;

  SELECT COUNT(*) INTO v_preklapanje
  FROM seansa s
  WHERE s.id_fizioterapeuta = v_ft
    AND s.id_seanse <> p_id_seanse
    AND s.status IN ('zahtevana', 'zakazana', 'odrzana')
    AND s.pocetak < DATE_ADD(v_pocetak, INTERVAL v_trajanje MINUTE)
    AND DATE_ADD(s.pocetak, INTERVAL s.trajanje_min MINUTE) > v_pocetak;

  IF v_preklapanje > 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Termin se preklapa sa postojećom seansom.';
  END IF;

  UPDATE seansa
  SET status = 'zakazana'
  WHERE id_seanse = p_id_seanse;

  COMMIT;
END$$

CREATE PROCEDURE sp_evidentiraj_uplatu(
  IN p_id_pacijenta INT,
  IN p_id_plana INT,
  IN p_id_seanse INT,
  IN p_iznos DECIMAL(10, 2),
  IN p_nacin VARCHAR(20),
  IN p_napomena VARCHAR(255)
)
BEGIN
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    RESIGNAL;
  END;

  START TRANSACTION;

  IF p_iznos IS NULL OR p_iznos <= 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Iznos uplate mora biti veći od nule.';
  END IF;

  IF NOT EXISTS (SELECT 1 FROM pacijent WHERE id_pacijenta = p_id_pacijenta) THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pacijent ne postoji.';
  END IF;

  INSERT INTO placanje (
    id_pacijenta, id_plana, id_seanse, tip, iznos, datum, nacin, napomena
  ) VALUES (
    p_id_pacijenta, p_id_plana, p_id_seanse, 'uplata', p_iznos, NOW(), p_nacin, p_napomena
  );

  COMMIT;
END$$

DELIMITER ;

INSERT INTO deo_tela (naziv, grupa) VALUES
  ('vrat', 'kičma'),
  ('vratna kičma', 'kičma'),
  ('gornja leđa', 'kičma'),
  ('donja leđa', 'kičma'),
  ('slabinska kičma', 'kičma'),
  ('levo rame', 'gornji ekstremitet'),
  ('desno rame', 'gornji ekstremitet'),
  ('leva nadlaktica', 'gornji ekstremitet'),
  ('desna nadlaktica', 'gornji ekstremitet'),
  ('levi lakat', 'gornji ekstremitet'),
  ('desni lakat', 'gornji ekstremitet'),
  ('leva podlaktica', 'gornji ekstremitet'),
  ('desna podlaktica', 'gornji ekstremitet'),
  ('levi zglob šake', 'gornji ekstremitet'),
  ('desni zglob šake', 'gornji ekstremitet'),
  ('leva šaka', 'gornji ekstremitet'),
  ('desna šaka', 'gornji ekstremitet'),
  ('grudi', 'trup'),
  ('stomak', 'trup'),
  ('karlica', 'trup'),
  ('levi kuk', 'donji ekstremitet'),
  ('desni kuk', 'donji ekstremitet'),
  ('leva natkolenica', 'donji ekstremitet'),
  ('desna natkolenica', 'donji ekstremitet'),
  ('levo koleno', 'donji ekstremitet'),
  ('desno koleno', 'donji ekstremitet'),
  ('leva potkolenica', 'donji ekstremitet'),
  ('desna potkolenica', 'donji ekstremitet'),
  ('levi skočni zglob', 'donji ekstremitet'),
  ('desni skočni zglob', 'donji ekstremitet'),
  ('levo stopalo', 'donji ekstremitet'),
  ('desno stopalo', 'donji ekstremitet');

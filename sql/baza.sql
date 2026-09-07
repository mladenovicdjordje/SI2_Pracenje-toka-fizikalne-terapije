/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: kinetika
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `korisnik`
--

LOCK TABLES `korisnik` WRITE;
/*!40000 ALTER TABLE `korisnik` DISABLE KEYS */;
INSERT INTO `korisnik` (`id_korisnika`, `email`, `lozinka_hash`, `uloga`, `aktivan`, `datum_kreiranja`) VALUES (4,'djordje@kinetika.rs','$2y$12$S0z/S8guVB7swhcSM8dCKe1CJX25S6cQcj91DmU9P1Ysvw.9Kvqgi','admin',1,'2026-09-06 18:17:30'),
(5,'marko@kinetika.rs','$2y$12$OQ1bsdoZBQu0/pacrhH8PeqK5tmUVBPk0F1Y5OYEsMtl9ak8O766y','fizioterapeut',1,'2026-09-06 18:17:30'),
(6,'petar@kinetika.rs','$2y$12$7qgSLFVTiH1MP3aWVe3wV.g9UaqiXeKvLA3PoXqmNv3PR/CBXomh.','fizioterapeut',1,'2026-09-06 18:17:30'),
(7,'mihajlo@mail.rs','$2y$12$uZPGeZcEhjb34bOWk4/Btu45Sx7aDKhkX01uAYaHcFdkiff/r0qNa','pacijent',0,'2026-09-06 18:17:31'),
(8,'milena@mail.rs','$2y$12$F.fsKNxWLPuMm.tBb.Oqd.SDSlpaFYw5L908/Q4ZceItkIUOKJpeq','pacijent',1,'2026-09-06 18:17:31'),
(10,'ana@kinetika.rs','$2y$12$T4eIiQ2GCD84g6RhlVzU/.hzH0Vzqiveyq6nurUaivAP7T8ngg9Y6','fizioterapeut',1,'2026-09-06 20:10:40'),
(11,'nikola@kinetika.rs','$2y$12$T4eIiQ2GCD84g6RhlVzU/.hzH0Vzqiveyq6nurUaivAP7T8ngg9Y6','fizioterapeut',1,'2026-09-06 20:10:40'),
(12,'jelena@kinetika.rs','$2y$12$T4eIiQ2GCD84g6RhlVzU/.hzH0Vzqiveyq6nurUaivAP7T8ngg9Y6','fizioterapeut',1,'2026-09-06 20:10:40'),
(13,'stefan@kinetika.rs','$2y$12$T4eIiQ2GCD84g6RhlVzU/.hzH0Vzqiveyq6nurUaivAP7T8ngg9Y6','fizioterapeut',1,'2026-09-06 20:10:40'),
(14,'milica@kinetika.rs','$2y$12$T4eIiQ2GCD84g6RhlVzU/.hzH0Vzqiveyq6nurUaivAP7T8ngg9Y6','fizioterapeut',1,'2026-09-06 20:10:40'),
(15,'lazar@kinetika.rs','$2y$12$T4eIiQ2GCD84g6RhlVzU/.hzH0Vzqiveyq6nurUaivAP7T8ngg9Y6','fizioterapeut',1,'2026-09-06 20:10:40'),
(16,'marija@kinetika.rs','$2y$12$T4eIiQ2GCD84g6RhlVzU/.hzH0Vzqiveyq6nurUaivAP7T8ngg9Y6','fizioterapeut',1,'2026-09-06 20:10:40'),
(17,'dusan@kinetika.rs','$2y$12$T4eIiQ2GCD84g6RhlVzU/.hzH0Vzqiveyq6nurUaivAP7T8ngg9Y6','fizioterapeut',1,'2026-09-06 20:10:40'),
(18,'sofija@kinetika.rs','$2y$12$T4eIiQ2GCD84g6RhlVzU/.hzH0Vzqiveyq6nurUaivAP7T8ngg9Y6','fizioterapeut',1,'2026-09-06 20:10:40'),
(19,'milan.tomic@gmail.com','$2y$12$9TjZqnkXqu09zPWa2DcTme8YFSrxd2waKh0zFEXSiwnuxk4Gva5by','pacijent',1,'2026-09-06 20:13:02'),
(20,'katarina.kostic@gmail.com','$2y$12$hKOpDsNQHEvUAk9yB4rdeeSQDIUufy0gb5VmnHdXPFmr7pg2Zv13W','pacijent',1,'2026-09-06 20:13:20'),
(21,'aleksandar.petrovic@gmail.com','$2y$12$nNGOpBjchxVlsJOyri2eS..NPJ/8ZJjAeDxW3OScZGsN6pE9f.GbO','pacijent',1,'2026-09-06 20:13:37'),
(22,'teodora.nikolic@gmail.com','$2y$12$kfe2I3z95p69PGnQaoIN3.ejgK7rp1uWKBM7xBtFfGB9206zQScJ6','pacijent',1,'2026-09-06 20:13:57'),
(23,'uros.stankovic@gmail.com','$2y$12$IXKpoDVWsQvyHLtVBFfPSukwzypjYLhrQ5NObQXP1hIrt/JlsL48i','pacijent',1,'2026-09-06 20:14:15'),
(24,'sara.ilic@gmail.com','$2y$12$AvMnEZdurK5m8Bbyf3SBuuB2R9Qt1L/09CG1t6NCFTYWUgETiJgBa','pacijent',1,'2026-09-06 20:14:31'),
(25,'filip.ristic@gmail.com','$2y$12$HtOEs32ME5EBKjKn8W/TieKh1yt0AHejh26LCTMX/jkPf8BV7WWA6','pacijent',1,'2026-09-06 20:14:50'),
(26,'dunja.milosevic@gmail.com','$2y$12$slM38G9Xeki/yk6CEDCRiuxtivhuUs.4kqdzbLfriNIohSRl42sc6','pacijent',1,'2026-09-06 20:15:04'),
(27,'nemanja.markovic@gmail.com','$2y$12$YvmcV8LAW88MTx3gqwKDV.027Iwom2l45FWg4xfCpx9BSmwcW2pPO','pacijent',1,'2026-09-06 20:15:25'),
(28,'isidora.djordjevic@gmail.com','$2y$12$oZjlwH2JoShHQRjYxS1kTeD5usFZNmDkQcnn6eie0cw7/kdtO4S7m','pacijent',1,'2026-09-06 20:15:42');
/*!40000 ALTER TABLE `korisnik` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `fizioterapeut`
--

LOCK TABLES `fizioterapeut` WRITE;
/*!40000 ALTER TABLE `fizioterapeut` DISABLE KEYS */;
INSERT INTO `fizioterapeut` (`id_fizioterapeuta`, `id_korisnika`, `ime`, `prezime`, `telefon`) VALUES (2,5,'Marko','Markovic','+381 62 222 598'),
(3,6,'Petar','Petrovic','+381 65 158 787'),
(4,10,'Ana','Jovanović','+381 65 222 3344'),
(5,11,'Nikola','Nikolić','+381 63 333 4455'),
(6,12,'Jelena','Đorđević','+381 60 444 5566'),
(7,13,'Stefan','Ilić','+381 62 555 6677'),
(8,14,'Milica','Pavlović','+381 61 666 7788'),
(9,15,'Lazar','Marković','+381 64 777 8899'),
(10,16,'Marija','Popović','+381 65 888 9900'),
(11,17,'Dušan','Stojanović','+381 63 999 0011'),
(12,18,'Sofija','Živković','+381 60 101 2023');
/*!40000 ALTER TABLE `fizioterapeut` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `pacijent`
--

LOCK TABLES `pacijent` WRITE;
/*!40000 ALTER TABLE `pacijent` DISABLE KEYS */;
INSERT INTO `pacijent` (`id_pacijenta`, `id_korisnika`, `id_fizioterapeuta`, `ime`, `prezime`, `telefon`) VALUES (2,7,2,'Mihajlo','Mihajlovic',NULL),
(3,8,2,'Milena','Aleksic','060123456'),
(5,19,7,'Milan','Tomić','+381 64 123 4567'),
(6,20,5,'Katarina','Kostić','+381 65 234 5678'),
(7,21,8,'Aleksandar','Petrović','+381 63 345 6789'),
(8,22,6,'Teodora','Nikolić','+381 60 456 7890'),
(9,23,6,'Uroš','Stanković','+381 62 567 8901'),
(10,24,9,'Sara','Ilić','+381 61 678 9012'),
(11,25,10,'Filip','Ristić','+381 64 789 0123'),
(12,26,10,'Dunja','Milošević','+381 65 890 1234'),
(13,27,9,'Nemanja','Marković','+381 63 901 2345'),
(14,28,7,'Isidora','Đorđević','+381 60 012 3456');
/*!40000 ALTER TABLE `pacijent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `deo_tela`
--

LOCK TABLES `deo_tela` WRITE;
/*!40000 ALTER TABLE `deo_tela` DISABLE KEYS */;
INSERT INTO `deo_tela` (`id_dela_tela`, `naziv`, `grupa`) VALUES (1,'Vrat','Kičma'),
(2,'Vratna kičma','Kičma'),
(3,'Gornja leđa','Kičma'),
(4,'Donja leđa','Kičma'),
(5,'Slabinska kičma','Kičma'),
(6,'Levo rame','Gornji ekstremitet'),
(7,'Desno rame','Gornji ekstremitet'),
(8,'Leva nadlaktica','Gornji ekstremitet'),
(9,'Desna nadlaktica','Gornji ekstremitet'),
(10,'Levi lakat','Gornji ekstremitet'),
(11,'Desni lakat','Gornji ekstremitet'),
(12,'Leva podlaktica','Gornji ekstremitet'),
(13,'Desna podlaktica','Gornji ekstremitet'),
(14,'Levi zglob šake','Gornji ekstremitet'),
(15,'Desni zglob šake','Gornji ekstremitet'),
(16,'Leva šaka','Gornji ekstremitet'),
(17,'Desna šaka','Gornji ekstremitet'),
(18,'Grudi','Trup'),
(19,'Stomak','Trup'),
(20,'Karlica','Trup'),
(21,'Levi kuk','Donji ekstremitet'),
(22,'Desni kuk','Donji ekstremitet'),
(23,'Leva natkolenica','Donji ekstremitet'),
(24,'Desna natkolenica','Donji ekstremitet'),
(25,'Levo koleno','Donji ekstremitet'),
(26,'Desno koleno','Donji ekstremitet'),
(27,'Leva potkolenica','Donji ekstremitet'),
(28,'Desna potkolenica','Donji ekstremitet'),
(29,'Levi skočni zglob','Donji ekstremitet'),
(30,'Desni skočni zglob','Donji ekstremitet'),
(31,'Levo stopalo','Donji ekstremitet'),
(32,'Desno stopalo','Donji ekstremitet');
/*!40000 ALTER TABLE `deo_tela` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `povreda`
--

LOCK TABLES `povreda` WRITE;
/*!40000 ALTER TABLE `povreda` DISABLE KEYS */;
INSERT INTO `povreda` (`id_povrede`, `id_pacijenta`, `id_dela_tela`, `tip`, `datum_povrede`, `tezina`, `opis`) VALUES (3,3,31,'Uganuće','2026-09-04','teska','Doživeo sam uganuće levog stopala kada mi se zglob iznenada iskrenuo pri nepravilnom doskoku. Odmah sam osetio oštar bol sa spoljašnje strane zgloba, a ubrzo su se pojavili otok i modrice koji su mi otežali oslanjanje na nogu i kretanje. Trenutno primenjujem protokol odmora, držim led da smanjim otok, nosim elastični zavoj radi kompresije i držim nogu podignutu kako bih ublažio nelagodnost.'),
(4,2,30,'Uganuće','2026-09-06','teska','Nezgodno sam stao, noga mi je propala i izvrnula se ka unutra, tako da sad jedva stojim na njoj. Odmah je počelo da boli, a zglob je otekao i izbila je modrica sa spoljne strane. Drastično mi je lakše kad odmorim i stavim led, ali svaki korak i pomeranje stopala i dalje bole za sve pare.'),
(5,5,4,'operacija','2026-09-01','umerena','Hronična napetost i bol u donjem delu leđa usled dugotrajnog sedenja.'),
(6,5,4,'operacija','2026-09-01','umerena','Hronična napetost i bol u donjem delu leđa usled dugotrajnog sedenja.'),
(7,6,26,'operacija','2026-08-15','teska','Postoperativni oporavak nakon artroskopije meniskusa desnog kolena.'),
(8,7,2,'uganuće','2026-09-05','blaga','Cervikalni sindrom praćen ukočenošću i glavoboljama.'),
(9,8,5,'uganuće','2026-07-10','umerena','Blago istegnuće ligamenata i mišića u predelu slabinske kičme.'),
(10,9,7,'prelom','2026-09-02','teska','Sanacija sportske povrede i jačanje rotatorne manžetne desnog ramena.'),
(11,10,4,'operacija','2026-08-01','umerena','Dekompresija i rasterećenje lumbalnog dela kičme.'),
(12,11,30,'uganuće','2026-09-06','umerena','Uganuće desnog skočnog zgloba tokom rekreativnog trčanja.'),
(13,12,21,'uganuće','2026-08-20','umerena','Bol u predelu levog kuka i glutealne regije.'),
(14,13,3,'prelom','2026-06-01','blaga','Posturalna kifoza i pogrbljenost gornjeg dela leđa.'),
(15,14,11,'operacija','2026-09-03','blaga','Sindrom lateralnog epikondilitisa (teniski lakat).');
/*!40000 ALTER TABLE `povreda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `terapijski_plan`
--

LOCK TABLES `terapijski_plan` WRITE;
/*!40000 ALTER TABLE `terapijski_plan` DISABLE KEYS */;
INSERT INTO `terapijski_plan` (`id_plana`, `id_povrede`, `id_pacijenta`, `id_fizioterapeuta`, `cilj`, `datum_od`, `datum_do`, `predvidjen_broj_seansi`, `status`, `nacin_naplate`, `cena`, `napomena`) VALUES (3,3,3,2,'Smanjenje bola','2026-09-06','2026-10-06',12,'aktivan','po_seansi',0.00,NULL),
(4,4,2,2,'Smanjenje bola','2026-09-06','2026-10-20',15,'aktivan','po_seansi',0.00,NULL),
(5,6,5,7,'Smanjenje bola i jačanje mišića trupa i leđa.','2026-09-01','2026-10-01',10,'aktivan','po_seansi',0.00,'Redovno izvoditi vežbe istezanja kod kuće.'),
(6,7,6,5,'Vraćanje punog obima pokreta i stabilnosti zgloba.','2026-08-15','2026-09-15',12,'aktivan','po_seansi',0.00,'Izbegavati dubok čučanj i opterećenje.'),
(7,8,7,8,'Oslobađanje napetosti u vratnom delu i korekcija držanja.','2026-09-05','2026-09-25',8,'aktivan','po_seansi',0.00,'Primeniti manuelnu masažu i laganu trakciju.'),
(8,9,8,6,'Uklanjanje akutnog bola i blaga elektroterapija.','2026-07-10','2026-08-10',10,'nacrt','po_seansi',0.00,'Uspešno završena terapija, pacijent bez tegoba.'),
(9,10,9,6,'Povećanje mišićne snage i stabilizacija zgloba ramena.','2026-09-02','2026-10-02',15,'aktivan','po_seansi',0.00,'Koristiti elastične trake progresivnog otpora.'),
(10,11,10,9,'Oslobađanje pritiska na nervne korene i otklanjanje bola.','2026-08-01','2026-09-01',10,'aktivan','po_seansi',0.00,'Kontrola za 3 meseca.'),
(11,12,11,10,'Povratak balansa, propioceptivni trening i puna funkcija.','2026-09-06','2026-10-06',10,'aktivan','po_seansi',0.00,'Obavezno nošenje stabilizatora pri hodu van kuće.'),
(12,13,12,10,'Vežbe jačanja abduktora kuka i mobilizacija karlice.','2026-08-20','2026-09-20',12,'aktivan','po_seansi',0.00,'Izbegavati dugotrajno stajanje na jednoj nozi.'),
(13,14,13,9,'Jačanje torakalne ekstenzije i vežbe za lopatice.','2026-06-01','2026-07-01',10,'aktivan','po_seansi',0.00,'Korigovana postura, nastavio sa samostalnim vežbama.'),
(14,15,14,7,'Ekscentrične vežbe podlaktice i smanjenje upale tetiva.','2026-09-03','2026-10-03',8,'aktivan','po_seansi',0.00,'Primena ultrazvučne terapije na početku svake seanse.');
/*!40000 ALTER TABLE `terapijski_plan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `vezba`
--

LOCK TABLES `vezba` WRITE;
/*!40000 ALTER TABLE `vezba` DISABLE KEYS */;
INSERT INTO `vezba` (`id_vezbe`, `naziv`, `grupa_misica`, `opis`, `kontraindikacije`, `id_kreirao`, `aktivna`) VALUES (4,'Čučanj','Kvadricepsi','Spuštanje karlice unazad i nadole uz savijanje kolena, održavajući prava leđa i oslonac na punom stopalu.','Oštećenja hrskavice kolena, diskus hernija, hronični bolovi u lumbalnom delu.',4,1),
(5,'Sklek','Grudi','Spuštanje i podizanje pravog tela pomoću ruku iz pozicije visokog planka, uz laktove usmerene blago unazad.','Povrede rotatorne manžetne ramena, sindrom karpalnog tunela, upale lakatnog zgloba.',4,1),
(6,'Mrtvo dizanje','Zadnja loža','Podizanje tereta sa poda snažnom ekstenzijom kukova i kolena, uz strogo održavanje neutralne kičme.','Diskus hernija, išijas, akutne povrede zadnje lože.',4,1),
(7,'Izdržaj','Trbušni mišići','Statično zadržavanje tela u pravoj liniji oslanjajući se na podlaktice i prste na nogama, uz kontrakciju stomaka.','Nedavne operacije abdomena, kile (hernije), povrede ramena.',4,1),
(8,'Zgibovi','Široki leđni mišić','Vertikalno povlačenje tela nagore na vratilu sve dok brada ne pređe visinu šipke.','Tendinitis bicepsa, povrede ramena, hronični bolovi u laktovima.',4,1),
(9,'Iskorak','Kvadricepsi','Koračanje jednom nogom napred uz spuštanje zadnjeg kolena ka podu, dok prednje koleno ne pređe liniju prstiju.','Povrede ligamenata kolena (ACL/MCL), problemi sa balansom i vrtoglavicom.',4,1),
(10,'Veslanje u pretklonu','Zadnje rame','Povlačenje tereta prema stomaku iz pozicije pretklona, spajajući lopatice u završnoj fazi pokreta.','Akutni bolovi u donjem delu leđa, povrede lumbalnog pršljena.',4,1),
(11,'Potisak iznad glave','Ramena','Guranje tereta od nivoa ključne kosti pravo iznad glave do potpunog ispravljanja ruku.','Impingement sindrom ramena, povrede vratne kičme.',4,1),
(12,'Podizanje na prste stojeći (Stoj na prstima)','List','Stanite uspravno (možete se osloniti rukama o zid za ravnotežu). Polako se podignite visoko na prste, zadržite poziciju na vrhu sekund-dve, pa se kontrolisano spustite petama nazad na pod.','Akutna upala Ahilove tetive, teška istegnuća mišića lista.',4,1),
(13,'Podizanje na prste sedeći','List','Sedite na stolicu sa stopalima ravno na podu, a tegove (ili opterećenje) postavite na donji deo natkolenica blizu kolena. Podižite pete od poda uz oslonac na prste, a zatim ih polako spustite.','Akutne povrede skočnog zgloba.',4,1),
(14,'Hodanje na petama','Prednji potkolenični mišić','Stanite uspravno i podignite prste stopala visoko od poda tako da se oslanjate isključivo na pete. Hodajte napred na petama u ravnommernom ritmu tokom određenog vremena ili dužine.','Plantarni fascitis u akutnoj fazi, oštri bolovi u skočnom zglobu.',4,1),
(16,'Most (Glute bridge)','Zadnjica','Lezite na leđa sa savijenim kolenima i stopalima postavljenim na pod u širini kukova. Stegnite gluteus i podignite karlicu ka plafonu dok telo ne formira ravnu liniju od ramena do kolena. Zadržite tren, pa spustite karlicu nazad.','Akutne povrede donjeg dela leđa ili zadnje lože.',4,1),
(17,'Bočni iskorak','Preponski mišići','Stanite uspravno, pa napravite širok korak u stranu jednom nogom. Savijajte koleno te iste noge i gurajte kukove unazad, dok druga noga ostaje potpuno opružena. Vratite se u početni položaj odgurivanjem.','Povrede prepona, nestabilnost kolena.',4,1),
(18,'Kruženje skočnim zglobom','Stabilizatori skočnog zgloba','Sedite ili stojte oslonjeni na jednu nogu, a drugu podignite malo od poda. Kružite stopalom u smeru kazaljke na satu 10 do 15 puta, a zatim promenite smer. Pokret treba da bude širok i kontrolisan.','Sveže iščašenje (zategnuće/uganuće) skočnog zgloba u akutnoj fazi.',4,1),
(19,'Vežba sa elastičnom trakom (Dorsifleksija)','Prednji potkolenični mišić','Sedite na pod sa ispruženim nogama. Zakačite elastičnu traku oko prednjeg dela stopala, a drugi kraj fiksirajte ispred sebe. Polako vucite prste ka sebi, pa se opirite otporu trake dok vraćate stopalo.','Akutni bol ili otok u skočnom zglobu.',4,1),
(20,'Jednonogi balans na nestabilnoj podlozi','Stabilizatori skočnog zgloba','Stanite na jednu nogu na ravnoj površini (ili na balans-jastuku ako ste napredniji) i pokušajte da održite ravnotežu 30 do 60 sekundi sa blago savijenim kolenom.','Nesposobnost samostalnog stajanja, akutna nestabilnost zgloba.',4,1),
(21,'Ekstenzija kolena sedeći (Zatezanje kvadricepsa)','Prednji deo natkolenice','Sedite na stolicu sa stopalima na podu. Polako ispravljajte jednu nogu u kolenu dok ne bude paralelna sa podom, zategnite mišić na vrhu na 2-3 sekunde, pa je spustite nazad.','Akutni patelofemoralni sindrom sa jakim bolom, skorašnje operacije meniskusa (bez dozvole lekara).',4,1),
(22,'Pregib kolena u stojećem položaju (Zadnja loža)','Zadnja loža','Stanite uspravno držeći se za naslon stolice. Savijte jednu nogu u kolenu zabacujući petu prema zadnjici, držeći butine paralelne. Vratite stopalo na pod.','Akutna povreda zadnje lože.',4,1),
(23,'Zidni čučanj (Wall sit)','Prednji deo natkolenice','Naslonite leđa ravno na zid i klizite nadole dok vam kolena ne budu pod uglom od 90 stepeni (ili manjim ako je preteško). Zadržite taj položaj dok dišete ravnomerno.','Teške povrede kolenih ligamenata (ACL/PCL), patelofemoralni artritis u akutnoj fazi.',4,1),
(24,'Skupljanje peškira prstima','Mišići stopala','Postavite mali peškir na gladak pod ispred sebe. Sedite na stolicu sa stopalima na peškiru. Koristeći samo prste stopala, gužvajte i privlačite peškir ka sebi.','Plantarni fascitis u akutnoj upalnoj fazi (može pojačati bol).',4,1),
(25,'Rolanje stopala preko loptice','Taban','U stojećem ili sedećem položaju postavite tenisku lopticu (ili lopticu sa bodljama) ispod tabana. Lagano pritiskajte stopalom i kotrljajte lopticu napred-nazad od pete do prstiju.','Otvorene rane na stopalu, akutni prelom kostiju stopala.',4,1),
(26,'Odvajanje palca (Toe spread out)','Mišići stopala','Stanite ili sedite sa stopalima ravno na podu. Pokušajte da odvojite palac od ostalih prstiju, a zatim i da raširite sve prste što je šire moguće, držeći stopalo fiksiranim na tlu.','Teški deformiteti prstiju (čukljevi) u fazi jakog bola.',4,1),
(27,'Biceps pregib sa bućicama','Prednja strana nadlaktice','Stanite uspravno sa bućicama u rukama, dlanovi okrenuti napred. Savijajte ruke u laktovima podižući tegove ka ramenima, držeći laktove fiksirane uz telo. Polako vratite u početni položaj.','Akutna tendinitis tetive bicepsa.',4,1),
(28,'Triceps ekstenzija iznad glave','Zadnja strana nadlaktice','Držite jednu bućicu objema rukama iza glave sa savijenim laktovima. Podignite teg iznad glave opružajući ruke u laktovima, držeći nadlaktice blizu ušiju, pa spustite nazad.','Problemi sa ramenima ili laktovima praćeni bolom pri elevaciji ruku.',4,1),
(29,'Sklekovi sa užim stavom (Triceps sklekovi)','Zadnja strana nadlaktice','Zauzmite položaj skleka sa rukama postavljenim u širini grudi ili uže. Spuštajte telo držeći laktove blizu tela, a zatim se odgurnite nazad u početni položaj.','Akutna povreda zgloba šake ili lakta (teniski lakat).',4,1),
(30,'Pregib ručnog zgloba sa dlanovima nagore (Wrist curl)','Unutrašnja strana podlaktice','Sedite sa podlakticama oslonjenim na bedra ili klupu, držeći bućice u rukama tako da dlanovi gledaju nagore, a šake vise preko ivice. Spustite tegove otvaranjem šake, pa ih savijanjem ručnog zgloba podignite nagore.','Sindrom karpalnog kanala u akutnoj fazi, akutni tendinitis fleksora.',4,1),
(31,'Obrnuti pregib ručnog zgloba (Wrist extension)','Spoljašnja strana podlaktice','Slično prethodnoj vežbi, ali su dlanovi okrenuti nadole (ka podu). Podižite ručni zglob nagore suprotstavljajući se težini bućice, pa ga kontrolisano spustite.','Sindrom teniskog lakta (lateralni epikondilitis) u akutnoj fazi.',4,1),
(32,'Uvrtanje štapa sa opterećenjem (Wrist roller)','Podlaktica','Držite štap sa užetom i tegom na kraju objema rukama ispred sebe u visini grudi. Naizmenično rotirajte štap napred i nazad da namotate i odmotate uže sa tegom.','Ozbiljne povrede tetiva šake i podlaktice.',4,1),
(33,'Stiskanje loptice za stres (Grip squeeze)','Šaka','Držite meku lopticu (ili gel-lopticu) u šaci. Snažno stisnite šaku i zadržite pritisak 3 do 5 sekundi, pa potpuno opustite stisak. Ponovite više puta.','Akutni artritis šake, skorašnje frakture kostiju šake.',4,1),
(34,'Širenje prstiju sa gumicom','Šaka','Postavite debelu gumicu oko spoljašnje strane svih pet prstiju blizu vrhova. Polako širite prste što je šire moguće protiv otpora gumice, pa ih skupite.','Akutne povrede tetiva ekstenzora prstiju.',4,1),
(35,'Dodirivanje palcem vrhova prstiju (Opozicija)','Šaka','Otvorite šaku, pa vrh palca redom spajajte sa vrhom kažiprsta, srednjeg prsta, domali i malog prsta, a potom klizite palcem niz bazu malog prsta.','Teški oblici reumatoidnog artritisa u fazi upale.',4,1),
(36,'Biceps pregib sa neutralnim hvatom (Hammer curl)','Prednja strana nadlaktice','Stanite držeći bućice sa dlanovima okrenutim ka telu (neutralan hvat). Savijajte ruke u laktovima podižući tegove ka ramenima, držeći zglobove fiksiranim, pa ih spustite.','Akutni medijalni ili lateralni epikondilitis.',4,1),
(37,'Otporna ekstenzija lakta (Triceps potisak na sajli)','Zadnja strana nadlaktice','Stanite ispred sajle sa pričvršćenim užetom. Držeći laktove fiksirane uz rebra, potiskujte uže nadole dok ruke ne budu potpuno opružene, pa se polako vratite u početni položaj.','Nestabilnost lakta, burzitis lakta.',4,1),
(38,'Izometrijska fleksija lakta','Prednja strana nadlaktice','Postavite ruku pod uglom od 90 stepeni u laktu. Drugom rukom pružite otpor pokušavajući da savijete ili ispravite ruku, dok prva ruka statički drži poziciju bez pokreta.','Akutne povrede mišića nadlaktice.',4,1),
(39,'Fleksija i ekstenzija ručnog zgloba bez opterećenja','Podlaktica','Ispružite ruku ispred sebe sa dlanom okrenutim napred. Drugom rukom nežno povucite prste unazad ka telu, zadržite 15-20 sekundi, a zatim okrenite dlan nadole i povucite prste ka sebi.','Akutna iščašenja ili prelomi u predelu ručnog zgloba.',4,1),
(40,'Ulnarna i radijalna devijacija (Mahanje)','Podlaktica','Postavite podlakticu na sto sa šakom koja visi preko ivice, dlan okrenut nadole. Pomerajte šaku levo-desno (u pravcu malog prsta pa palca) kao da mašete.','Akutni sindrom karpalnog tunela sa oštrim bolom.',4,1),
(41,'Kruženje ručnim zglobom','Zglob šake','Spojite šake ispred grudi ili ih držite opuštene, pa lagano kružite zglobovima šake u jednom smeru 10 puta, a zatim u suprotnom smeru.','Sveže povrede ligamenata zgloba šake.',4,1),
(42,'Odručenje bućicama (Bočno podizanje)','Rame','Stanite uspravno sa lakšim bućicama pored tela. Podižite ruke u stranu do visine ramena sa blagim savijanjem u laktovima, pa ih kontrolisano spustite.','Sindrom uklještenja ramena (impingement), akutna upala rotatorne manžetne.',4,1),
(43,'Potisak iznad glave sa bućicama (Military press)','Rame','Sedite ili stojte držeći bućice u visini ramena sa dlanovima napred. Potiskujte tegove vertikalno iznad glave dok ruke ne budu potpuno opružene, pa ih polako spustite nazad do ramena.','Teške povrede ramena, nestabilnost ramenskog zgloba.',4,1),
(44,'Spoljna rotacija ramena sa elastičnom trakom','Rame','Pričvrstite elastičnu traku u visini lakta. Stanite bočno, lakat ruke uz telo savijen pod uglom od 90 stepeni, i rotirajte podlakticu ka spolja, udaljavajući je od stomaka protiv otpora trake.','Akutna povreda tetiva rotatorne manžetne.',4,1),
(45,'Mačka-krava (Cat-Cow)','Donji deo leđa','Postavite se na šake i kolena (u položaj stojeći na četvoronoške). Udahnite i spuštajte stomak ka podu dok savijate leđa ka dole i gledate gore. Izdišite i izvijajte leđa ka plafonu uvlačeći bradu i stomak.','Akutni lumbalni išijas sa jakim sevanjem bola.',4,1),
(46,'Supermen (Leđna ekstenzija na stomaku)','Donji deo leđa','Lezite na stomak sa ispruženim rukama iznad glave. Istovremeno podignite ruke, grudni koš i opružene noge nekoliko centimetara od poda. Zadržite položaj 2 sekunde, pa se spustite.','Teška hernija diska, akutni lumbago.',4,1),
(47,'Rotacija kolena u ležećem položaju (Lumbalni twist)','Donji deo leđa','Lezite na leđa sa savijenim kolenima i stopalima na podu, ruke raširene u stranu. Polako spuštajte oba spojena kolena ka jednoj strani dok ramena ostaju na podu, zadržite, pa prebacite na drugu stranu.','Akutni lumbalni sindrom, nestabilnost karlice.',4,1),
(48,'Veslanje u pretklonu sa bućicama','Gornji deo leđa','Postavite se u pretklon sa blago savijenim kolenima i ravnim leđima. Držite bućice i vucite ih ka kukovima, lopatice skupljajući na vrhu pokreta, pa ih kontrolisano spustite.','Akutni bol u donjem i gornjem delu leđa.',4,1),
(49,'Skupljanje lopatica (Scapular retraction)','Gornji deo leđa','Stanite uspravno sa opuštenim rukama pored tela. Povucite ramena unazad i nadole, snažno spajajući lopatice kao da pokušavate da držite olovku između njih. Zadržite 3 sekunde i opustite se.','Akutni grčevi u gornjem delu leđa.',4,1),
(50,'Povlačenje na lat-mašini (Lat pulldown)','Široki leđni mišić','Sedite na mašinu i uhvatite šipku širim hvatom od ramena. Vucite šipku nadole ka gornjem delu grudi, šireći grudi i spajajući lopatice, pa je kontrolisano vratite gore.','Akutne povrede ramena ili lakta.',4,1),
(51,'Izometrijska fleksija i ekstenzija vrata','Vrat','Postavite dlan na čelo i blago gurajte glavu napred dok dlan pruža otpor bez pomeranja glave (zadržite 5 sekundi). Potom postavite šake na potiljak i radite isto gurajući glavu unazad.','Akutni tortikolis (krivi vrat), teške cervikalne diskus hernije.',4,1),
(52,'Bočno istezanje vrata','Vrat','Stanite uspravno. Nagnite uho ka ramenu jedne strane dok suprotno rame vučete nadole. Možete blago staviti ruku preko glave za blagi pritisak. Zadržite 20-30 sekundi, pa ponovite na drugu stranu.','Akutne trzajne povrede vrata (whiplash).',4,1),
(53,'Rotacija glave','Vrat','Polako okrenite glavu u stranu gledajući preko ramena, zadržite sekund, pa je lagano okrenite ka suprotnom ramenu. Pokreti moraju biti spori i kontrolisani, bez naglih trzaja.','Vrtoglavice, akutni cervikalni sindrom.',4,1),
(54,'Privlačenje kolena grudima (Lumbosakralna fleksija)','Donji deo leđa','Lezite na leđa sa savijenim kolenima. Rukama obuhvatite kolena i polako ih privucite ka grudima dok ne osetite blago istezanje u slabinskom delu. Zadržite 20 sekundi i opustite se.','Akutni išijas praćen jakim sevanjem (proveriti da li pogoršava simptome).',4,1),
(55,'Karlični nagib (Pelvic tilt)','Trbušni mišići','Lezite na leđa sa savijenim kolenima i stopalima na podu. Zategnite stomak i pritisnite donji deo leđa ravno na pod, blago podižući karlicu nagore. Zadržite 5 sekundi, pa otpustite.','Akutne povrede sakroilijakalnog zgloba sa jakim bolom.',4,1),
(56,'Most na jednoj nozi (Modifikovani)','Zadnjica','Lezite na leđa sa savijenim kolenima. Podignite karlicu ka gore kao kod običnog mosta, ali držite trup čvrstim i fokusiranim na neutralan položaj slabinske kičme bez preteranog izvijanja.','Akutni lumbalni bolovi.',4,1),
(57,'Uvlačenje brade (Chin tuck)','Vrat','Stanite ili sedite uspravno sa pogledom ravno napred. Povucite bradu unazad (kao da pravite dvostruku bradu) bez naginjanja glave nagore ili nadole. Zadržite poziciju 3-5 sekundi, pa opustite.','Akutni cervikalni bol praćen trnjenjem ruku.',4,1),
(58,'Bočni otpor vrata','Vrat','Postavite dlan sa strane glave iznad uha. Gurajte glavu ka dlanu dok ruka pruža ravnomeran otpor, ne dozvoljavajući glavi da se pomera. Zadržite 5 sekundi, pa promenite stranu.','Teška nestabilnost vratnih pršljenova.',4,1),
(59,'Istezanje gornjeg trapeza i levatora skapule','Vrat','Okrenite glavu za 45 stepeni u jednu stranu, a zatim spustite bradu ka pazuhu te iste strane. Blago spustite ruku preko potiljka za pojačano istezanje. Zadržite 20 sekundi.','Akutni grčevi mišića vrata.',4,1),
(60,'Klasični sklekovi','Grudi','Zauzmite položaj planka sa šakama postavljenim nešto šire od širine ramena. Spuštajte telo ka podu držeći trup čvrstim i ravnim, dok grudi skoro ne dotaknu tlo, pa se odgurnite nazad.','Akutne povrede ramena, lakta ili ručnog zgloba.',4,1),
(61,'Potisak sa ravne klupe sa bućicama (Bench press)','Grudi','Lezite na ravnu klupu držeći bućice u visini sredine grudi sa dlanovima napred. Potiskujte tegove nagore iznad grudi dok se ruke ne ispruže, pa ih polako spustite u početni položaj.','Teške povrede rotatorne manžetne ili ramenskog zgloba.',4,1),
(62,'Razvlačenje sa bućicama (Chest fly)','Grudi','Lezite na ravnu klupu sa bućicama iznad grudi, sa blago savijenim laktovima. Polako spuštajte ruke u lukovima ka spolja dok ne osetite istezanje u grudima, pa ih spojite nazad iznad grudi.','Akutne povrede tetive pektoralisa ili ramena.',4,1),
(63,'Otvaranje kuka u ležećem položaju (Školjka)','Zadnjica','Lezite na bok sa savijenim kolenima pod uglom od 90 stepeni i stopalima spojenim. Držeći stopala zajedno, podižite gornje koleno ka plafonu kao školjka koja se otvara, pa ga spustite.','Akutni bursitis kuka, teške povrede karlice.',4,1),
(64,'Bočni plank sa osloncem na koleno','Bočni trbušni mišići','Lezite na bok oslonjeni na podlakticu i spoljni deo savijenog kolena (donja noga je savijena pod 90 stepeni). Podignite karlicu od poda tako da telo formira ravnu liniju, i zadržite položaj.','Akutni bol u ramenu ili donjem delu leđa.',4,0),
(65,'Stabilizacija karlice u stojećem položaju (Flossing karlice)','Karlica','Stanite uspravno sa blago savijenim kolenima. Svesno pomerajte karlicu napred (uvlačeći trticu) pa nazad (blago izvijajući), a potom levo-desno, pronalazeći neutralnu tačku stabilnosti.','Akutne frakture karličnih kostiju.',4,1),
(66,'Trbušnjaci (Standardni crunch)','Stomak','Lezite na leđa sa savijenim kolenima i stopalima na podu. Postavite ruke iza glave ili prekrstite na grudima. Podižite samo gornji deo leđa i lopatice od poda stežući stomak, pa se kontrolisano spustite.','Razdvojeni trbušni mišići (dijastaza rekti), teške diskus hernije.',4,1),
(67,'Plank (Izometrijski izdržaj)','Stomak','Postavite se u položaj oslonca na podlaktice i prste stopala. Telo mora biti potpuno ravno od glave do peta, stomak i gluteus čvrsto stisnuti. Zadržite položaj u ravnomernom disanju.','Akutni bol u donjem delu leđa ili povrede ramena.',4,1),
(68,'Ruski uski zaokret (Russian twist)','Bočni trbušni mišići','Sedite na pod sa savijenim kolenima i stopalima odignutim od poda (ili blago oslonjenim). Nagnite trup unazad pod uglom od 45 stepeni i rotirajte trup levo-desno, dodirujući pod rukama (ili tegom) pored tela.','Teške povrede i hernija lumbalnog dela kičme.',4,1);
/*!40000 ALTER TABLE `vezba` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `plan_vezba`
--

LOCK TABLES `plan_vezba` WRITE;
/*!40000 ALTER TABLE `plan_vezba` DISABLE KEYS */;
INSERT INTO `plan_vezba` (`id_plan_vezba`, `id_plana`, `id_vezbe`, `id_dela_tela`, `serije`, `ponavljanja`, `napomena`) VALUES (3,14,43,11,3,10,NULL),
(4,14,10,11,3,10,NULL),
(5,14,7,11,3,10,NULL),
(6,13,9,3,3,10,NULL),
(7,13,8,3,3,10,NULL),
(8,13,6,4,3,10,NULL),
(9,12,4,21,3,10,NULL),
(10,12,9,21,3,10,NULL),
(11,12,47,21,3,10,NULL),
(12,11,13,30,3,10,NULL),
(13,11,12,30,3,10,NULL),
(14,11,26,30,3,10,NULL),
(15,10,9,4,3,10,NULL),
(16,10,7,4,3,10,NULL),
(17,9,68,7,3,10,NULL),
(18,9,8,7,3,10,NULL),
(19,9,6,7,3,10,NULL),
(20,8,4,5,3,10,NULL),
(21,8,60,5,3,10,NULL),
(22,7,6,2,3,10,NULL),
(23,7,53,2,3,10,NULL),
(24,7,10,2,3,10,NULL),
(25,6,19,26,3,10,NULL);
/*!40000 ALTER TABLE `plan_vezba` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `seansa`
--

LOCK TABLES `seansa` WRITE;
/*!40000 ALTER TABLE `seansa` DISABLE KEYS */;
INSERT INTO `seansa` (`id_seanse`, `id_plana`, `id_pacijenta`, `id_fizioterapeuta`, `pocetak`, `trajanje_min`, `status`, `nivo_bola`, `cena`, `napomena`, `otkazao_uloga`) VALUES (27,9,9,6,'2026-09-07 08:00:00',60,'odrzana',NULL,0.00,NULL,NULL),
(28,14,14,7,'2026-09-07 09:15:00',60,'odrzana',NULL,0.00,NULL,NULL),
(29,10,10,9,'2026-09-07 10:30:00',60,'odrzana',NULL,0.00,NULL,NULL),
(30,3,3,2,'2026-09-07 10:30:00',60,'odrzana',NULL,0.00,NULL,NULL),
(31,12,12,10,'2026-09-07 13:00:00',60,'odrzana',NULL,0.00,NULL,NULL),
(32,7,7,8,'2026-09-07 14:15:00',60,'otkazana',NULL,0.00,NULL,'admin'),
(33,9,9,6,'2026-09-08 08:00:00',60,'zakazana',NULL,0.00,NULL,NULL),
(34,6,6,5,'2026-09-08 08:00:00',60,'zakazana',NULL,0.00,NULL,NULL),
(35,11,11,10,'2026-09-08 09:15:00',60,'zakazana',NULL,0.00,NULL,NULL),
(36,11,11,10,'2026-09-08 11:45:00',60,'zakazana',NULL,0.00,NULL,NULL),
(37,10,10,9,'2026-09-08 10:30:00',60,'zakazana',NULL,0.00,NULL,NULL),
(38,13,13,9,'2026-09-08 11:45:00',60,'zakazana',NULL,0.00,NULL,NULL),
(39,10,10,9,'2026-09-09 11:45:00',60,'zakazana',NULL,0.00,NULL,NULL),
(40,13,13,9,'2026-09-09 13:00:00',60,'zakazana',NULL,0.00,NULL,NULL),
(41,10,10,9,'2026-09-10 09:15:00',60,'zakazana',NULL,0.00,NULL,NULL),
(42,10,10,9,'2026-09-10 15:30:00',60,'zakazana',NULL,0.00,NULL,NULL),
(43,13,13,9,'2026-09-11 13:00:00',60,'zakazana',NULL,0.00,NULL,NULL),
(44,10,10,9,'2026-09-11 11:45:00',60,'zakazana',NULL,0.00,NULL,NULL),
(45,10,10,9,'2026-09-12 10:30:00',60,'zakazana',NULL,0.00,NULL,NULL),
(46,13,13,9,'2026-09-12 11:45:00',60,'zakazana',NULL,0.00,NULL,NULL),
(47,3,3,2,'2026-09-08 11:45:00',60,'zakazana',NULL,0.00,NULL,NULL),
(48,3,3,2,'2026-09-09 10:30:00',60,'zakazana',NULL,0.00,NULL,NULL),
(49,3,3,2,'2026-09-10 13:00:00',60,'zakazana',NULL,0.00,NULL,NULL),
(50,3,3,2,'2026-09-11 11:45:00',60,'zakazana',NULL,0.00,NULL,NULL),
(51,3,3,2,'2026-09-12 13:00:00',60,'zakazana',NULL,0.00,NULL,NULL),
(52,3,3,2,'2026-09-11 16:45:00',60,'zakazana',NULL,0.00,NULL,NULL),
(53,14,14,7,'2026-09-08 11:45:00',60,'zakazana',NULL,0.00,NULL,NULL),
(54,5,5,7,'2026-09-08 10:30:00',60,'otkazana',NULL,0.00,NULL,'pacijent'),
(55,5,5,7,'2026-09-09 15:30:00',60,'zakazana',NULL,0.00,NULL,NULL),
(56,14,14,7,'2026-09-09 14:15:00',60,'zakazana',NULL,0.00,NULL,NULL),
(57,14,14,7,'2026-09-11 11:45:00',60,'zakazana',NULL,0.00,NULL,NULL),
(58,5,5,7,'2026-09-11 14:15:00',60,'zakazana',NULL,0.00,NULL,NULL),
(59,5,5,7,'2026-09-12 11:45:00',60,'zakazana',NULL,0.00,NULL,NULL),
(60,14,14,7,'2026-09-12 14:15:00',60,'zakazana',NULL,0.00,NULL,NULL),
(61,14,14,7,'2026-09-10 14:15:00',60,'zakazana',NULL,0.00,NULL,NULL),
(62,14,14,7,'2026-09-10 15:30:00',60,'zakazana',NULL,0.00,NULL,NULL),
(63,7,7,8,'2026-09-08 13:00:00',60,'zakazana',NULL,0.00,NULL,NULL),
(64,7,7,8,'2026-09-10 16:45:00',60,'zakazana',NULL,0.00,NULL,NULL),
(65,7,7,8,'2026-09-11 08:00:00',60,'zakazana',NULL,0.00,NULL,NULL),
(66,7,7,8,'2026-09-12 08:00:00',60,'zakazana',NULL,0.00,NULL,NULL),
(67,12,12,10,'2026-09-09 13:00:00',60,'zakazana',NULL,0.00,NULL,NULL),
(68,11,11,10,'2026-09-09 14:15:00',60,'zakazana',NULL,0.00,NULL,NULL),
(69,12,12,10,'2026-09-10 11:45:00',60,'zakazana',NULL,0.00,NULL,NULL),
(70,11,11,10,'2026-09-10 13:00:00',60,'zakazana',NULL,0.00,NULL,NULL),
(71,12,12,10,'2026-09-11 10:30:00',60,'zakazana',NULL,0.00,NULL,NULL),
(72,12,12,10,'2026-09-11 14:15:00',60,'zakazana',NULL,0.00,NULL,NULL),
(73,12,12,10,'2026-09-12 13:00:00',60,'zakazana',NULL,0.00,NULL,NULL),
(74,12,12,10,'2026-09-12 14:15:00',60,'zakazana',NULL,0.00,NULL,NULL),
(75,11,11,10,'2026-09-12 15:30:00',60,'zakazana',NULL,0.00,NULL,NULL),
(76,5,5,7,'2026-09-09 09:15:00',60,'zahtevana',NULL,0.00,NULL,NULL),
(77,5,5,7,'2026-09-09 10:30:00',60,'zahtevana',NULL,0.00,NULL,NULL),
(78,5,5,7,'2026-09-10 09:15:00',60,'zahtevana',NULL,0.00,NULL,NULL),
(79,5,5,7,'2026-09-10 10:30:00',60,'zahtevana',NULL,0.00,NULL,NULL),
(80,5,5,7,'2026-09-07 14:15:00',60,'odrzana',5,0.00,NULL,NULL),
(81,5,5,7,'2026-09-07 15:30:00',60,'zahtevana',NULL,0.00,NULL,NULL);
/*!40000 ALTER TABLE `seansa` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-07 10:47:15

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2023 at 11:07 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pokemon`
--

-- --------------------------------------------------------

--
-- Table structure for table `battles`
--

CREATE TABLE `battles` (
  `idBattle` int(11) NOT NULL,
  `idPokemonDefendant` int(11) DEFAULT NULL,
  `idPokemonChallenger` int(11) NOT NULL,
  `idWinner` int(11) DEFAULT NULL,
  `arena` varchar(255) NOT NULL,
  `idTrainerChallenger` int(11) NOT NULL,
  `idTrainerDefendant` int(11) NOT NULL,
  `turn` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `battles`
--

INSERT INTO `battles` (`idBattle`, `idPokemonDefendant`, `idPokemonChallenger`, `idWinner`, `arena`, `idTrainerChallenger`, `idTrainerDefendant`, `turn`) VALUES
(1, 1, 2, 2, 'Pallet Arena', 0, 0, 0),
(2, 1, 3, 1, 'Cerulean Arena', 0, 0, 0),
(3, 2, 3, NULL, 'Pewter Arena', 0, 0, 0),
(4, 4, 5, 4, 'Petalburg Arena', 0, 0, 0),
(5, 5, 6, 6, 'Twinleaf Arena', 0, 0, 0),
(6, NULL, 551, NULL, 'Wilderness', 0, 1, 0),
(7, NULL, 117, NULL, 'Wilderness', 0, 1, 0),
(8, NULL, 504, NULL, 'Wilderness', 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pokemon`
--

CREATE TABLE `pokemon` (
  `idPokemon` int(11) NOT NULL,
  `idSpecies` int(11) NOT NULL,
  `idTrainer` int(11) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `level` int(11) NOT NULL,
  `experience` int(11) NOT NULL DEFAULT 0,
  `health` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pokemon`
--

INSERT INTO `pokemon` (`idPokemon`, `idSpecies`, `idTrainer`, `nickname`, `level`, `experience`, `health`) VALUES
(3, 7, 2, 'Squirt', 9, 130, 75),
(4, 25, 2, 'Zap', 12, 180, 90),
(5, 133, 3, 'Rocky', 11, 160, 85),
(6, 258, 3, 'Geo', 7, 90, 60),
(7, 255, 4, 'Eeve', 15, 250, 100),
(8, 252, 4, 'Char', 18, 300, 120),
(9, 16, 5, 'Torchy', 6, 80, 55),
(10, 19, 6, 'Pip', 7, 90, 60);

-- --------------------------------------------------------

--
-- Table structure for table `species`
--

CREATE TABLE `species` (
  `idSpecies` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `species`
--

INSERT INTO `species` (`idSpecies`, `name`, `type`, `description`) VALUES
(1, 'Bulbasaur', 'Grass/Poison', 'A strange seed was planted on its back at birth. The plant sprouts and grows with this Pokémon.'),
(4, 'Charmander', 'Fire', 'The flame that burns at the tip of its tail is an indication of its emotions. The flame wavers when Charmander is enjoying itself.'),
(7, 'Squirtle', 'Water', 'When it retracts its long neck into its shell, it squirts out water with vigorous force.'),
(16, 'Pidgey', 'Normal/Flying', 'Very docile. If attacked, it will often kick up sand to protect itself rather than fight back.'),
(19, 'Rattata', 'Normal', 'Bites anything when it attacks. Small and very quick, it is a common sight in many places.'),
(25, 'Pikachu', 'Electric', 'This Pokémon has electricity-storing pouches on its cheeks. These appear to become electrically charged during the night while Pikachu sleeps.'),
(133, 'Eevee', 'Normal', 'Eevee has an unstable genetic makeup that suddenly mutates due to the environment in which it lives. Radiation from various stones causes this Pokémon to evolve.'),
(252, 'Treecko', 'Grass', 'Treecko is cool, calm, and collected – it never panics under any situation. If a bigger foe were to glare at this Pokémon, it would glare right back without conceding an inch of ground.'),
(255, 'Torchic', 'Fire', 'Torchic sticks with its Trainer, following behind with unsteady steps. This Pokémon breathes fire of over 1,800 degrees Fahrenheit, including fireballs that leave the foe scorched black.'),
(258, 'Mudkip', 'Water', 'The fin on Mudkip’s head acts as highly sensitive radar. Using this fin to sense movements of water and air, this Pokémon can determine what is taking place around it without using its eyes.');

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `idTrainer` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `hometown` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`idTrainer`, `username`, `birthdate`, `gender`, `hometown`, `password`) VALUES
(0, 'Bot', '2023-10-09', 'Other', 'Wilderness', ''),
(1, 'tom', '1990-03-04', 'Male', 'Pallet Town', '$2y$10$aQRhXeiRG.wXklj69S8fDe8uo3e5C9ysnyJYHe6VMrI5dnhimDT9m'),
(2, 'MistyWaterflower', '1988-12-15', 'Female', 'Cerulean City', 'hashed_password_2'),
(3, 'BrockRock', '1986-09-08', 'Male', 'Pewter City', 'hashed_password_3'),
(4, 'GaryOak', '1989-07-03', 'Male', 'Pallet Town', 'hashed_password_4'),
(5, 'MayMaple', '2002-04-18', 'Female', 'Petalburg City', 'hashed_password_5'),
(6, 'DawnHikari', '2003-12-29', 'Female', 'Twinleaf Town', 'hashed_password_6');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `battles`
--
ALTER TABLE `battles`
  ADD PRIMARY KEY (`idBattle`),
  ADD KEY `idWinner` (`idWinner`),
  ADD KEY `idPokemonChallenger` (`idPokemonChallenger`) USING BTREE,
  ADD KEY `idPokemonDefendant` (`idPokemonDefendant`) USING BTREE,
  ADD KEY `idTrainerDefendant` (`idTrainerDefendant`),
  ADD KEY `idTrainerChallenger` (`idTrainerChallenger`);

--
-- Indexes for table `pokemon`
--
ALTER TABLE `pokemon`
  ADD PRIMARY KEY (`idPokemon`),
  ADD KEY `idSpecies` (`idSpecies`),
  ADD KEY `idTrainer` (`idTrainer`);

--
-- Indexes for table `species`
--
ALTER TABLE `species`
  ADD PRIMARY KEY (`idSpecies`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`idTrainer`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `battles`
--
ALTER TABLE `battles`
  MODIFY `idBattle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pokemon`
--
ALTER TABLE `pokemon`
  MODIFY `idPokemon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `idTrainer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `battles`
--
ALTER TABLE `battles`
  ADD CONSTRAINT `battles_ibfk_3` FOREIGN KEY (`idWinner`) REFERENCES `trainers` (`idTrainer`);

--
-- Constraints for table `pokemon`
--
ALTER TABLE `pokemon`
  ADD CONSTRAINT `pokemon_ibfk_1` FOREIGN KEY (`idSpecies`) REFERENCES `species` (`idSpecies`),
  ADD CONSTRAINT `pokemon_ibfk_2` FOREIGN KEY (`idTrainer`) REFERENCES `trainers` (`idTrainer`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

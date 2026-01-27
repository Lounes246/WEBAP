-- Create messages table for the messaging system
CREATE TABLE IF NOT EXISTS `messages` (
  `idMessage` INT(11) NOT NULL AUTO_INCREMENT,
  `idSender` INT(11) NOT NULL,
  `idReceiver` INT(11) NOT NULL,
  `messageText` TEXT NOT NULL,
  `isRead` TINYINT(1) DEFAULT 0,
  `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idMessage`),
  FOREIGN KEY (`idSender`) REFERENCES `trainers`(`idTrainer`) ON DELETE CASCADE,
  FOREIGN KEY (`idReceiver`) REFERENCES `trainers`(`idTrainer`) ON DELETE CASCADE,
  INDEX (`idSender`),
  INDEX (`idReceiver`),
  INDEX (`createdAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

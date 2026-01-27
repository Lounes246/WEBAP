-- Create global_chat table for the global chat system
CREATE TABLE IF NOT EXISTS `global_chat` (
  `idMessage` INT(11) NOT NULL AUTO_INCREMENT,
  `idSender` INT(11) NOT NULL,
  `messageText` TEXT NOT NULL,
  `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idMessage`),
  FOREIGN KEY (`idSender`) REFERENCES `trainers`(`idTrainer`) ON DELETE CASCADE,
  INDEX (`idSender`),
  INDEX (`createdAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

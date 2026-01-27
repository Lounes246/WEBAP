-- Create a table to store trainers
CREATE TABLE trainers (
    idTrainer INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    birthdate DATE,
    gender ENUM('Male', 'Female', 'Other'),
    hometown VARCHAR(255),
    passwordHash VARCHAR(255) NOT NULL
);

-- Insert trainer data with birthdates
INSERT INTO trainers (username, birthdate, gender, hometown, passwordHash) VALUES
    ('AshKetchum', '1990-03-04', 'Male', 'Pallet Town', 'hashed_password_1'),
    ('MistyWaterflower', '1988-12-15', 'Female', 'Cerulean City', 'hashed_password_2'),
    ('BrockRock', '1986-09-08', 'Male', 'Pewter City', 'hashed_password_3'),
    ('GaryOak', '1989-07-03', 'Male', 'Pallet Town', 'hashed_password_4'),
    ('MayMaple', '2002-04-18', 'Female', 'Petalburg City', 'hashed_password_5'),
    ('DawnHikari', '2003-12-29', 'Female', 'Twinleaf Town', 'hashed_password_6');

-- Create a table to store Pokémon species data
CREATE TABLE species (
    idSpecies INT  PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    description TEXT
);

-- Insert Pokémon species data
INSERT INTO species (idSpecies,name, type, description) VALUES
     (1, 'Bulbasaur', 'Grass/Poison', 'A strange seed was planted on its back at birth. The plant sprouts and grows with this Pokémon.'),
    (4, 'Charmander', 'Fire', 'The flame that burns at the tip of its tail is an indication of its emotions. The flame wavers when Charmander is enjoying itself.'),
    (7, 'Squirtle', 'Water', 'When it retracts its long neck into its shell, it squirts out water with vigorous force.'),
    (25, 'Pikachu', 'Electric', 'This Pokémon has electricity-storing pouches on its cheeks. These appear to become electrically charged during the night while Pikachu sleeps.'),
    (133, 'Eevee', 'Normal', 'Eevee has an unstable genetic makeup that suddenly mutates due to the environment in which it lives. Radiation from various stones causes this Pokémon to evolve.'),
    (258, 'Mudkip', 'Water', 'The fin on Mudkip’s head acts as highly sensitive radar. Using this fin to sense movements of water and air, this Pokémon can determine what is taking place around it without using its eyes.'),
    (255, 'Torchic', 'Fire', 'Torchic sticks with its Trainer, following behind with unsteady steps. This Pokémon breathes fire of over 1,800 degrees Fahrenheit, including fireballs that leave the foe scorched black.'),
    (252, 'Treecko', 'Grass', 'Treecko is cool, calm, and collected – it never panics under any situation. If a bigger foe were to glare at this Pokémon, it would glare right back without conceding an inch of ground.'),
    (16, 'Pidgey', 'Normal/Flying', 'Very docile. If attacked, it will often kick up sand to protect itself rather than fight back.'),
    (19, 'Rattata', 'Normal', 'Bites anything when it attacks. Small and very quick, it is a common sight in many places.');

-- Create a table to store individual Pokémon
CREATE TABLE pokemon (
    idPokemon INT AUTO_INCREMENT PRIMARY KEY,
    idSpecies INT NOT NULL,
    idTrainer INT NOT NULL,
    nickname VARCHAR(255),
    level INT NOT NULL,
    experience INT NOT NULL DEFAULT 0,
    health INT NOT NULL,
    FOREIGN KEY (idSpecies) REFERENCES species(idSpecies),
    FOREIGN KEY (idTrainer) REFERENCES trainers(idTrainer)
);

-- Insert Pokémon data
INSERT INTO pokemon (idSpecies, idTrainer, nickname, level, experience, health) VALUES
    (1, 1, 'Pika', 8, 100, 70),
    (4, 1, 'Bulby', 10, 150, 80),
    (7, 2, 'Squirt', 9, 130, 75),
    (25, 2, 'Zap', 12, 180, 90),
    (133, 3, 'Rocky', 11, 160, 85),
    (258, 3, 'Geo', 7, 90, 60),
    (255, 4, 'Eeve', 15, 250, 100),
    (252, 4, 'Char', 18, 300, 120),
    (16, 5, 'Torchy', 6, 80, 55),
    (19, 6, 'Pip', 7, 90, 60);

-- Create a table to store battle history
CREATE TABLE battles (
    idBattle INT AUTO_INCREMENT PRIMARY KEY,
    idTrainer1 INT NOT NULL,
    idTrainer2 INT NOT NULL,
    idWinner INT,
    arena VARCHAR(255) NOT NULL,
    FOREIGN KEY (idTrainer1) REFERENCES trainers(idTrainer),
    FOREIGN KEY (idTrainer2) REFERENCES trainers(idTrainer),
    FOREIGN KEY (idWinner) REFERENCES trainers(idTrainer)
);

-- Insert battle data
INSERT INTO battles (idTrainer1, idTrainer2, idWinner, arena) VALUES
    (1, 2, 2, 'Pallet Arena'),
    (1, 3, 1, 'Cerulean Arena'),
    (2, 3, NULL, 'Pewter Arena'),
    (4, 5, 4, 'Petalburg Arena'),
    (5, 6, 6, 'Twinleaf Arena');

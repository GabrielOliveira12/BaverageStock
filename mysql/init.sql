SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE TABLE IF NOT EXISTS `sections` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `type` ENUM('alcoholic', 'non_alcoholic') NOT NULL,
    `max_capacity` DECIMAL(10,2) NOT NULL,
    `current_volume` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_section_type` (`name`, `type`)
);

CREATE TABLE IF NOT EXISTS `beverage_types` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `section_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`section_id`) REFERENCES `sections`(`id`) ON DELETE RESTRICT,
    UNIQUE KEY `unique_beverage_type` (`name`)
);

CREATE TABLE IF NOT EXISTS `beverages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `brand` VARCHAR(255) NOT NULL,
    `volume_per_unit` DECIMAL(10,2) NOT NULL,
    `quantity` INT NOT NULL DEFAULT 0,
    `total_volume` DECIMAL(10,2) GENERATED ALWAYS AS (`volume_per_unit` * `quantity`) STORED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `beverage_links` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `beverage_id` INT NOT NULL,
    `section_id` INT NOT NULL,
    `beverage_type_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`beverage_id`) REFERENCES `beverages`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`section_id`) REFERENCES `sections`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`beverage_type_id`) REFERENCES `beverage_types`(`id`) ON DELETE RESTRICT,
    UNIQUE KEY `unique_beverage_section` (`beverage_id`, `section_id`)
);

CREATE TABLE IF NOT EXISTS `history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `operation_type` ENUM('entry', 'exit') NOT NULL,
    `beverage_id` INT NOT NULL,
    `section_id` INT NOT NULL,
    `beverage_type_id` INT NOT NULL,
    `quantity` INT NOT NULL,
    `volume` DECIMAL(10,2) NOT NULL,
    `responsible` VARCHAR(255) NOT NULL,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`beverage_id`) REFERENCES `beverages`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`section_id`) REFERENCES `sections`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`beverage_type_id`) REFERENCES `beverage_types`(`id`) ON DELETE RESTRICT,
    INDEX `idx_history_date` (`created_at`),
    INDEX `idx_history_section` (`section_id`),
    INDEX `idx_history_type` (`beverage_type_id`)
);

INSERT IGNORE INTO `sections` (`name`, `type`, `max_capacity`) VALUES
('Secao Alcoolicas A', 'alcoholic', 500000.00),
('Secao Nao Alcoolicas A', 'non_alcoholic', 400000.00);

INSERT IGNORE INTO `beverage_types` (`name`, `section_id`) VALUES
('Cerveja', 1),
('Vinho', 1),
('Whisky', 1),
('Vodka', 1),
('Refrigerante', 2),
('Suco Natural', 2),
('Agua', 2),
('Energetico', 2);
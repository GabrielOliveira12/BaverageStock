IF NOT EXISTS (SELECT * FROM information_schema.tables WHERE table_name = 'sections') THEN
    CREATE TABLE `sections` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `created_at` DATETIME NOT NULL,
        `created_by` VARCHAR(255) NOT NULL,
        `altered_at` DATETIME DEFAULT NULL,
        `altered_by` VARCHAR(255) DEFAULT NULL
);

IF NOT EXISTS (SELECT * FROM information_schema.tables WHERE table_name = 'beverages_types') THEN
    CREATE TABLE `beverages_types` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `created_at` DATETIME NOT NULL,
        `created_by` VARCHAR(255) NOT NULL,
        `altered_at` DATETIME DEFAULT NULL,
        `altered_by` VARCHAR(255) DEFAULT NULL
);

IF NOT EXISTS (SELECT * FROM information_schema.tables WHERE table_name = 'beverages') THEN
    CREATE TABLE `beverages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `capacity` VARCHAR(50) NOT NULL,
        `brand` VARCHAR(255) NOT NULL,
        `created_at` DATETIME NOT NULL,
        `created_by` VARCHAR(255) NOT NULL,
        `altered_at` DATETIME DEFAULT NULL,
        `altered_by` VARCHAR(255) DEFAULT NULL
);

IF NOT EXISTS (SELECT * FROM information_schema.tables WHERE table_name = 'beverage_links') THEN
    CREATE TABLE `beverage_links` (
        `beverage_id` INT NOT NULL,
        `section_id` INT NOT NULL,
        `type_id` INT NOT NULL,
        `created_at` DATETIME NOT NULL,
        PRIMARY KEY (`beverage_id`, `section_id`),
        FOREIGN KEY (`beverage_id`) REFERENCES `beverages`(`id`),
    FOREIGN KEY (`section_id`) REFERENCES `sections`(`id`)
);
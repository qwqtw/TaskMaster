-- Table structure for table "list"
CREATE TABLE `list` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `title` VARCHAR(30) NOT NULL,
  `list_order` INT NOT NULL,
  `last_updated` INT DEFAULT NULL
);

-- Table structure for table "task"
CREATE TABLE `task` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `list_id` INT NOT NULL,
  `due_date` DATE DEFAULT NULL,
  `content` TEXT NOT NULL,
  `priority` INT NOT NULL DEFAULT 0,
  `is_completed` TINYINT(1) NOT NULL DEFAULT 0
);

-- Table structure for table "user"
CREATE TABLE `user` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(45) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `reg_date` DATE NOT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL
);

-- View structure for "view_user_task"
DROP VIEW IF EXISTS `view_user_task`;
CREATE VIEW `view_user_task` AS
  SELECT
    `task`.`id` AS `id`,
    `task`.`list_id` AS `list_id`,
    `task`.`due_date` AS `due_date`,
    `task`.`content` AS `content`,
    `task`.`priority` AS `priority`,
    `task`.`is_completed` AS `is_completed`,
    `list`.`user_id` AS `user_id`
  FROM `task`
  LEFT JOIN `list` ON `task`.`list_id` = `list`.`id`;

-- Add indexes for dumped tables
CREATE INDEX `list_user_id_index` ON `list` (`user_id`);
CREATE INDEX `task_list_id_index` ON `task` (`list_id`);

-- Add foreign key constraints
ALTER TABLE `list` ADD CONSTRAINT `list_user_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
ALTER TABLE `task` ADD CONSTRAINT `task_list_fk` FOREIGN KEY (`list_id`) REFERENCES `list` (`id`);

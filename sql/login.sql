/**
 * Creates roles table
 * 
 * NOTE: this includes admin, dealers, and any other future roles
*/
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_name` VARCHAR(32) NOT NULL,
  `role_description` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `uniq_role_name` (`role_name`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8 ;
/**
 * default `roles` values:
*/
INSERT INTO `roles` (`role_id`, `role_name`, `role_description`) VALUES
(NULL, 'login', 'Login privileges, granted after account confirmation'),
(NULL, 'dealer', 'Dealership user, has access to special pricing, etc.'),
(NULL, 'admin', 'Administrative user, has access to everything.');
 
 
/**
 * Creates users table
*/
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(127) NOT NULL,
  `username` VARCHAR(32) NOT NULL DEFAULT '',
  `password` CHAR(64) NOT NULL,
  `salt` BINARY(8) NOT NULL,
  `logins` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `last_login` INT(10) UNSIGNED DEFAULT NULL,
  `cookie` VARCHAR(32) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8 ;
 
/**
 * Creates roles_users table
 * 
 * NOTE: this is an intersection table between roles and users
*/
CREATE TABLE IF NOT EXISTS `roles_users` (
  `user_id` BIGINT UNSIGNED NOT NULL,
  `role_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  FOREIGN KEY (`user_id`) REFERENCES users(`user_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES roles(`role_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8;
 
/**
 * Creates password_reset_requests table
 * 
 * NOTE: This if for forgotten password resets
*/
CREATE TABLE IF NOT EXISTS `password_reset_requests` (
  `token`	CHAR(32) PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `expiration` TIMESTAMP NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES users(`user_id`)
);
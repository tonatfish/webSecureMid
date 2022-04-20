-- CREATE DATABASE webpage_mid CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- use webpage_mid;

CREATE TABLE `users` (
	`id` VARCHAR(36) PRIMARY KEY COMMENT 'UUID',
    `name` VARCHAR(20) NOT NULL COMMENT 'user name',
    `password` VARCHAR(100) NOT NULL COMMENT 'user password',
    `token` VARCHAR(100) COMMENT 'access token',
    `manager` boolean COMMENT 'is manager',
    `sticker` BLOB
);

CREATE TABLE `msgs` (
	`id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'auto increment ID',
    `userid` VARCHAR(36) NOT NULL COMMENT 'speaker id',
    `username`  VARCHAR(20) COMMENT 'user name',
    `content` VARCHAR(500) CHARACTER SET utf8mb4 COMMENT 'msg content(bbcode)',
    `filename` VARCHAR(60) COMMENT 'uploaded file name',
    `filetype` VARCHAR(20) COMMENT 'uploaded file type',
    `file` BLOB COMMENT 'attached file',
	FOREIGN KEY (`userid`) REFERENCES `users`(`id`)
);

CREATE TABLE `title` (
	`webtitle` VARCHAR(60) NOT NULL COMMENT 'web title'
);

INSERT INTO `users` VALUES('3fee487c-ded9-4990-ba07-9c0bf552792f', 'tonatfish', 'zhps600174', '', true, '');

INSERT INTO `msgs` VALUES('2', '3fee487c-ded9-4990-ba07-9c0bf552792f', 'tonatfish', 'ouo12345', '', '', '');

INSERT INTO `title` VALUES('please do not fight me');

-- SELECT * FROM `users`;
-- SELECT * FROM `msgs`;
-- SELECT * FROM `title`;

CREATE USER 'api_user'@'localhost' identified by 'api_password';
GRANT ALL on webpage_mid.* to 'api_user'@'localhost';
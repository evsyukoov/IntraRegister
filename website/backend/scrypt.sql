CREATE TABLE `intra`.`users` (
                                 `login` VARCHAR(45) NOT NULL,
                                 `email` VARCHAR(45) NULL,
                                 `status` INT NULL,
                                 `intra_id` INT NULL,
                                 PRIMARY KEY (`login`));

//без этого нет коннекта с  MySQL 8+ из PHP
ALTER USER root IDENTIFIED WITH mysql_native_password BY '1111';
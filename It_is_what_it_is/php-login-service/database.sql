/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/ dating_website /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE dating_website;

DROP TABLE IF EXISTS users;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `firstName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phoneNr` varchar(20) DEFAULT null,
  `profile_pic` VARCHAR(50) DEFAULT NULL,
  `username` varchar(50),

  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS userlogin;
CREATE TABLE `userlogin` (
  `id` int NOT NULL,
  `username` varchar(30) DEFAULT NULL,
  `pass` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE OR REPLACE VIEW `userlist` AS 
select `users`.`id` AS `userID`,
`userlogin`.`username` AS `username`,
`users`.`firstName` AS `firstName`,
`users`.`lastName` AS `lastName`,
`users`.`email` AS `email`,
`users`.`phoneNr` AS `phoneNr`,
`users`.`profile_pic` AS `profile_pic`
from ((`users`) 
left join `userlogin`
 on((`users`.`id` = `userlogin`.`id`)));



INSERT INTO userlogin(id,username,pass) VALUES(57,'Test','$2y$10$cg0PrkttTXI65aZ1SNImGeOUp4.THzqz8Cd489zDYb9x97RWFErsW');
INSERT INTO users(id,firstName,lastName,email)
VALUES(2,'Nicklas','Andersen','nicklas@mail.com'),
    (3,'Sarah','Dybvad','sarah@mail.com'),
    (4,'Alex','Handhiuc','alex@mail.com'),
    (5,'Piotr','Pospiech','piotr@mail.com'),
    (47,'Kasper',"Topp",'@mail.com'),
    (48,'Nanna',NULL,'nana@mail.com'),
    (49,'Lasse',NULL,NULL),
    (50,'Isla',NULL,NULL),
    (57,'','','Other');




DROP PROCEDURE IF EXISTS AddNewUser;

DELIMITER//
CREATE PROCEDURE `AddNewUser`(
    IN firstnameVar VARCHAR(100),
    IN countrynameVar VARCHAR(100)
)
BEGIN
    START TRANSACTION;
    IF
        NOT EXISTS (SELECT id FROM countries WHERE countryName = countrynameVar)
    THEN
        INSERT INTO countries (countryName) VALUES (countrynameVar);
    END IF;
    SET @countryID := (SELECT id FROM countries WHERE countryName = countrynameVar);
    INSERT INTO users (firstname, country)
    VALUES (firstnameVar, @countryID);
    COMMIT;
END//
DELIMITER;

DROP PROCEDURE IF EXISTS CreateUser;
CREATE PROCEDURE `CreateUser`(
    IN emailVar VARCHAR(50),
    IN usernameVar VARCHAR(30),
    IN passwordVar VARCHAR(60)
)
BEGIN
    START TRANSACTION;
        INSERT INTO users ( email,username)
        VALUES (emailVar,usernameVar);
 
        SET @userID := (SELECT id FROM users ORDER BY id DESC LIMIT 1);

        INSERT INTO userlogin (id, username, pass)
        VALUES (@userID, usernameVar, passwordVar);
    COMMIT;
END;

DROP PROCEDURE IF EXISTS CreateProfile;
CREATE PROCEDURE `CreateProfile`(
    IN users_id int,
    IN firstNameVar VARCHAR(50),
    IN lastNameVar VARCHAR(50),
    IN phoneNrVar VARCHAR(20)
)
BEGIN
    START TRANSACTION;

     UPDATE users SET 
     firstName = firstNameVar, 
     lastName = lastNameVar, 
     phoneNr = phoneNrVar

     WHERE id = users_id;

    COMMIT;
END//



Drop PROCEDURE if EXISTS EditUser;
DELIMITER//
Create PROCEDURE `EditUser`(
    IN users_id int,
    IN firstNameVar VARCHAR(50),
    IN lastNameVar VARCHAR(50),
    IN phoneNrVar VARCHAR(20),
    IN emailVar varchar(50)
)
BEGIN
    START TRANSACTION;
        if(firstNameVar<>"") then
            UPDATE users SET 
            firstName = firstNameVar 
            
            WHERE id = users_id;
        end if;
        if(lastNameVar<>"") then
            UPDATE users SET 
            lastName = lastNameVar

            WHERE id = users_id;
        end if;
         if(phoneNrVar<>"") then
            UPDATE users SET 
            phoneNr = phoneNrVar

            WHERE id = users_id;
        end if;
         if(emailVar<>"") then
            UPDATE users SET 
            email = emailVar

            WHERE id = users_id;
        end if;
    COMMIT;
END//
DELIMITER;


Drop PROCEDURE if EXISTS UpdatePassword;
DELIMITER//
CREATE PROCEDURE `UpdatePassword`(
    IN userID int,
    IN pwd varchar(60)
)
BEGIN
  START TRANSACTION;

     UPDATE userlogin SET 
     pass = pwd

     WHERE id = userID;

    COMMIT;
END//

DELIMITER;




delimiter//


CREATE PROCEDURE `DeleteUser`(
    in userID int
)
BEGIN
  START TRANSACTION;

    Delete from users
    WHERE id = userID;

    Delete from userlogin
    WHERE id = userID;
    COMMIT;
END;
delimiter;



DROP TABLE IF EXISTS Posts;
CREATE TABLE `Posts` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `title` varchar(50) DEFAULT NULL,
  `Item_description` varchar(500) DEFAULT NULL,
  `price` VARCHAR(50) DEFAULT NULL,
  `THEaddress` varchar(50) DEFAULT null,
  `post_pic` VARCHAR(50) DEFAULT NULL,
  `attributed_category` int DEFAULT NULL,
  `phone_nr` varchar(20),

  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS category;
CREATE TABLE `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vagen` int DEFAULT NULL,
  `vagetarian` int DEFAULT NULL,
  `meal` int DEFAULT NULL,
  `dairy` int DEFAULT NULL,
  `pastry` int DEFAULT NULL,
  `fruit` int DEFAULT NULL,
  `vegetables` int DEFAULT NULL,
  `glutenFre` int DEFAULT NULL,
  `lactoseFree` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) 
;




DROP PROCEDURE IF EXISTS CreatePost;
CREATE PROCEDURE `CreatePost`(
    IN titleVar VARCHAR(50),
    IN descriptionVar VARCHAR(500),
    IN priceVar varchar(50),
    IN addressVar VARCHAR(60),
    IN phone_nrVar VARCHAR(20)
    
)
BEGIN
    START TRANSACTION;
if (priceVar = "") THEN 
INSERT INTO Posts ( title,Item_description,price,THEaddress,phone_nr)
        VALUES (titleVar,descriptionVar,NULL,addressVar,phone_nrVar);
END If;
if (addressVar = "") THEN 
INSERT INTO Posts ( title,Item_description,price,THEaddress,phone_nr)
        VALUES (titleVar,descriptionVar,priceVar,NULL,phone_nrVar);
END If;

if((titleVar<>"" ) and (descriptionVar<>"") and (priceVar<>"") and (addressVar<>"") and (phone_nrVar<>"")) then
        INSERT INTO Posts ( title,Item_description,price,THEaddress,phone_nr)
        VALUES (titleVar,descriptionVar,priceVar,addressVar,phone_nrVar);
end if;
 
    COMMIT;
END;





INSERT INTO Posts ( title,Item_description,price,THEaddress,phone_nr)
VALUES ("Tomatoes","Organic tomatoes just picked up from the garden.  We harvest more than can consume therefore we're giving it away ",'10','Vesterbrogade 15','45972164'),
("Carrots","Carrots for sale. Bought a few days ago.","null","Brennerpasset 34","91643055"),
("Sweets","Sweets for sale","15","Vejlevej 4","75347482"),
("Apples"," Lots of aples are growing on tree. Come and pick them up before it gets wasted ","","Skovvangen 42","40653217"),
("Milk","0,4% milk","5","Hovbanken 77",""),
("Cookies ","2 kilograms box with cookies ","30","Rynkebyvej 89",""),
("Ginger","Ginger","15","Søndre Havnevej 84","29437156"),
("Carrots","Pick it up","10","Funkevænget 61","30642943"),
("Bread","Bread for sale","10","","76240811")
;






Insert Into Posts ( title,Item_description,price,THEaddress) values ("Tomatoes","Organic tomatoes just picked up from the garden. We harvest more than can consume therefore we're giving it away ", "10","Vesterbrogade 15")






























Delete from users WHERE id>57;

delete from userlogin where id>57;

Insert into users (id,firstName)
values (69,"dasd");

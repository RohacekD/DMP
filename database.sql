CREATE DATABASE requests
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;
use requests;
create table users(
 id int NOT NULL AUTO_INCREMENT,
 Name varchar(255) NOT NULL,
 Title varchar(10) DEFAULT NULL,
 First_name varchar(255) NOT NULL,
 Last_name varchar(255) NOT NULL,
 Pass varchar(255) NOT NULL,
 Email varchar(150) NOT NULL,
 Send boolean NOT NULL DEFAULT FALSE,
 Last_login TIMESTAMP,
 Access TINYINT,
 Style varchar(100),
 PRIMARY KEY (id)
);

create table requests(
 id int NOT NULL AUTO_INCREMENT,
 Name varchar(150) NOT NULL,
 Create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 Description varchar(1000) NOT NULL,
 Need FLOAT(2),
 User_id int NOT NULL,
 PRIMARY KEY (id),
 FOREIGN KEY (User_id) REFERENCES users(id)
 );

create table requests_confirm(
 User_id int NOT NULL,
 Request_id int NOT NULL,
 FOREIGN KEY (User_id) REFERENCES users(id),
 FOREIGN KEY (Request_id) REFERENCES requests(id)
);

insert into users VALUES (NULL, "Admin", NULL, "Admin", "Admin", MD5("ADMIN"), "Admin@localhost.cz", FALSE, null, 4, null);

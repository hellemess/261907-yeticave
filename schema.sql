CREATE DATABASE yeticave;

USE yeticave;

CREATE TABLE categories (
  title CHAR(15) PRIMARY KEY
);

CREATE TABLE lots (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  creation_date   DATETIME,
  title           CHAR(100),
  description     TEXT,
  picture         CHAR(20),
  starting_price  INT,
  expiration_date DATE,
  step            INT,
  likes           INT,
  seller          CHAR(100),
  winner          CHAR(100),
  category        CHAR(15)
);

CREATE TABLE bets (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  betting_date DATETIME,
  cost         INT,
  buyer        CHAR(100),
  lot          INT
);

CREATE TABLE users (
  email             CHAR(100) PRIMARY KEY,
  registration_date DATETIME,
  name              CHAR,
  password          CHAR(60),
  avatar            CHAR(20),
  contacts          TEXT
);

CREATE UNIQUE INDEX c_title ON categories(title);

CREATE UNIQUE INDEX u_email ON users(email);

CREATE INDEX l_title ON lots(title);

CREATE DATABASE yeticave
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE categories (
  id       INT (10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  category CHAR (15) NOT NULL,
  link     CHAR (15) NOT NULL
);

CREATE TABLE users (
  id                INT (10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  email             CHAR (100) NOT NULL,
  registration_date DATETIME,
  name              CHAR (100) NOT NULL,
  password          CHAR (60) NOT NULL,
  picture           CHAR (20),
  contacts          TEXT
);

CREATE TABLE lots (
  id              INT (10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  creation_date   DATETIME NOT NULL,
  title           CHAR (100) NOT NULL,
  description     VARCHAR(500) NOT NULL,
  picture         CHAR (20) NOT NULL,
  starting_price  INT NOT NULL,
  expiration_date DATE NOT NULL,
  step            INT NOT NULL,
  likes           INT,
  seller          INT (10) NOT NULL,
  winner          INT (10),
  category        INT (10) NOT NULL,
  FOREIGN KEY (seller) REFERENCES users (id),
  FOREIGN KEY (winner) REFERENCES users (id),
  FOREIGN KEY (category) REFERENCES categories (id)
);

CREATE TABLE bets (
  id           INT (10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  betting_date DATETIME NOT NULL,
  cost         INT NOT NULL,
  buyer        INT (10) NOT NULL,
  lot          INT (10) NOT NULL,
  FOREIGN KEY (buyer) REFERENCES users (id),
  FOREIGN KEY (lot) REFERENCES lots (id)
);

CREATE UNIQUE INDEX c_title ON categories (category);

CREATE UNIQUE INDEX u_email ON users (email);

CREATE INDEX l_title ON lots (title, description);

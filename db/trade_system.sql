# Create schema

CREATE SCHEMA IF NOT EXISTS trade_system;
USE trade_system;

# Create tables

CREATE TABLE IF NOT EXISTS Product
(
  id            INT NOT NULL AUTO_INCREMENT,
  title         VARCHAR(50),
  description   VARCHAR(500),
  category      INT,
  article       VARCHAR(50),
  barcode       VARCHAR(50),
  consignment   VARCHAR(50),
  manufacturer  VARCHAR(100),
  model         VARCHAR(50),
  series        VARCHAR(50),
  specification VARCHAR(50),
  comment       VARCHAR(256),
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS Characteristic
(
  id           INT NOT NULL AUTO_INCREMENT,
  type         VARCHAR(10),
  name         VARCHAR(30),
  measure      VARCHAR(50),
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS Category
(
  id     INT NOT NULL AUTO_INCREMENT,
  name   VARCHAR(50),
  parent INT,
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS Characteristic_Category
(
  characteristic INT,
  category       INT
);

CREATE TABLE IF NOT EXISTS Product_Characteristic
(
  product        INT NOT NULL,
  characteristic INT,
  value          VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS Instance
(
  id            INT NOT NULL AUTO_INCREMENT,
  current_count INT,
  buying_count  INT,
  buying_price  DECIMAL(18, 2),
  currency      VARCHAR(20),
  product       INT NOT NULL,
  storage       VARCHAR(20),
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS Transaction
(
  id             INT NOT NULL AUTO_INCREMENT,
  total_count    INT,
  type           TINYINT(1),
  total_price    DECIMAL(18, 2),
  document       VARCHAR(256),
  preparing_date DATE,
  conducted_date DATE,
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS Counterparty
(
  id         INT NOT NULL AUTO_INCREMENT,
  title      VARCHAR(50),
  type       TINYINT(1),
  firstname  VARCHAR(50),
  lastname   VARCHAR(50),
  middlename VARCHAR(50),
  phone      VARCHAR(20),
  email      VARCHAR(50),
  address    VARCHAR(100),
  JP_type    VARCHAR(50),
  JP_code    VARCHAR(50),
  comment    VARCHAR(256),
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS Instance_Transaction
(
  instance      INT NOT NULL,
  transaction   INT NOT NULL,
  counterparty  INT,
  type          TINYINT(1),
  selling_count INT,
  selling_price DECIMAL(18, 2)
);

# Create FKs

ALTER TABLE Product
  ADD FOREIGN KEY (category)
REFERENCES Category (id)
  ON UPDATE CASCADE
  ON DELETE SET NULL;

ALTER TABLE Product_Characteristic
  ADD FOREIGN KEY (product)
REFERENCES Product (id)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE Product_Characteristic
  ADD FOREIGN KEY (characteristic)
REFERENCES Characteristic (id)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE Characteristic_Category
  ADD FOREIGN KEY (category)
REFERENCES Category (id)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE Characteristic_Category
  ADD FOREIGN KEY (characteristic)
REFERENCES Characteristic (id)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE Instance
  ADD FOREIGN KEY (product)
REFERENCES Product (id)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE Instance_Transaction
  ADD FOREIGN KEY (transaction)
REFERENCES Transaction (id)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE Instance_Transaction
  ADD FOREIGN KEY (instance)
REFERENCES Instance (id)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE Instance_Transaction
  ADD FOREIGN KEY (counterparty)
REFERENCES Counterparty (id)
  ON UPDATE CASCADE
  ON DELETE SET NULL;

# Create schemas
CREATE SCHEMA IF NOT EXISTS trade_system;


# Create tables
CREATE TABLE IF NOT EXISTS trade_system.Product_Characteristic
(
    product INT NOT NULL,
    characteristic INT,
    value_bool TINYINT(1),
    value_int INT,
    value_double DOUBLE,
    value_string VARCHAR(50)    
);

CREATE TABLE IF NOT EXISTS trade_system.Product
(
    id INT NOT NULL UNIQUE,
    title VARCHAR(50),
    description VARCHAR(500),
    category INT,
    article VARCHAR(50),
    barcode VARCHAR(50),
    consignment VARCHAR(50),
    manufacturer VARCHAR(100),
    model VARCHAR(50),
    series VARCHAR(50),
    specification VARCHAR(50),
    comment VARCHAR(256),
    PRIMARY KEY(id)
);

CREATE TABLE IF NOT EXISTS trade_system.Transaction
(
    id INT NOT NULL,
    total_count INT,
    type TINYINT(1),
    total_price DECIMAL(18, 2),
    document VARCHAR(256),
    preparing_date DATE,
    conducted_date DATE,
    PRIMARY KEY(id)
);

CREATE TABLE IF NOT EXISTS trade_system.Instance_Transaction
(
    instance INT NOT NULL,
    transaction INT,
    counterparty INT,
    type TINYINT(1),
    selling_count INT,
    selling_price DECIMAL(18, 2)    
);

CREATE TABLE IF NOT EXISTS trade_system.Characteristic
(
    id INT NOT NULL,
    type VARCHAR(10),
    name VARCHAR(30),
    PRIMARY KEY(id)
);

CREATE TABLE IF NOT EXISTS trade_system.Category
(
    id INT NOT NULL,
    name VARCHAR(50),
    parent INT,
    PRIMARY KEY(id)
);

CREATE TABLE IF NOT EXISTS trade_system.Instance
(
    id INT NOT NULL,
    current_count INT,
    buying_count INT,
    buying_price DECIMAL(18, 2),
    product INT NOT NULL,
    storage VARCHAR(20),
    PRIMARY KEY(id)
);

CREATE TABLE IF NOT EXISTS trade_system.Counterparty
(
    id INT NOT NULL,
    title VARCHAR(50),
    type TINYINT(1),
    firstname VARCHAR(50),
    lastname VARCHAR(50),
    middlename VARCHAR(50),
    phone VARCHAR(20),
    email VARCHAR(50),
    address VARCHAR(100),
    JP_type VARCHAR(50),
    JP_code VARCHAR(50),
    comment VARCHAR(256),
    PRIMARY KEY(id)
);

CREATE TABLE IF NOT EXISTS trade_system.Characteristic_Category
(
    characteristic INT,
    category INT    
);


# Create FKs
ALTER TABLE trade_system.Product_Characteristic
    ADD    FOREIGN KEY (product)
    REFERENCES trade_system.Product(id)
;
    
ALTER TABLE trade_system.Instance_Transaction
    ADD    FOREIGN KEY (transaction)
    REFERENCES trade_system.Transaction(id)
;
    
ALTER TABLE trade_system.Product_Characteristic
    ADD    FOREIGN KEY (characteristic)
    REFERENCES trade_system.Characteristic(id)
;
    
ALTER TABLE trade_system.Product
    ADD    FOREIGN KEY (category)
    REFERENCES trade_system.Category(id)
;
    
ALTER TABLE trade_system.Instance
    ADD    FOREIGN KEY (product)
    REFERENCES trade_system.Product(id)
;
    
ALTER TABLE trade_system.Instance_Transaction
    ADD    FOREIGN KEY (instance)
    REFERENCES trade_system.Instance(id)
;
    
ALTER TABLE trade_system.Instance_Transaction
    ADD    FOREIGN KEY (counterparty)
    REFERENCES trade_system.Counterparty(id)
;
    
ALTER TABLE trade_system.Characteristic_Category
    ADD    FOREIGN KEY (category)
    REFERENCES trade_system.Category(id)
;
    
ALTER TABLE trade_system.Characteristic_Category
    ADD    FOREIGN KEY (characteristic)
    REFERENCES trade_system.Characteristic(id)
;
    

# Create Indexes


-- dummy data for development

LOCK TABLES `Category` WRITE;
INSERT INTO `Category` (name, parent) VALUES
  ('електроніка', 0),
  ('господарство', 0),
  ('телефони', 1),
  ('телевізори', 1),
  ('сад і город', 2);
UNLOCK TABLES;

LOCK TABLES `Characteristic` WRITE;
INSERT INTO `Characteristic` (type, name, measurements) VALUES
  ('float','діагональ','дм'),
  ('boolean','тв2 ',''),
  ('integer','кількість ядер','шт'),
  ('float','висота','м,см'),
  ('float','діаметр','м,см');
UNLOCK TABLES;

LOCK TABLES `Characteristic_Category` WRITE;
INSERT INTO `Characteristic_Category`(characteristic, category) VALUES
  (1,3),
  (3,3),
  (4,3),
  (1,4),
  (2,4),
  (4,4),
  (4,5),
  (5,5);
UNLOCK TABLES;

LOCK TABLES `Product` WRITE;
INSERT INTO `Product`(title, description, category, article, barcode, consignment,
                      manufacturer, model, series, specification, comment) VALUES
  ('Iphone',NULL,3,'12345','qwerty','23','Apple','7','3','cool',''),
  ('Xiaomi',NULL,3,'5434324','ytrewq','54','MI','Redmi 3','343','Norm',NULL),
  ('Smart TV',NULL,4,'fewrer','56564','4','Samsung','345s','78','fucking cool',NULL),
  ('Regular TV',NULL,4,'567','hthtrhtr','333','China Noname','first','so-so','FX',NULL),
  ('Горщик для квітів',NULL,5,'2324frfr','lflgflg','4','China nigers','3','',NULL,NULL),
  ('Граблі','',5,'455','544','3','Ukraine','nimbulus','2000','qwerty',NULL);
UNLOCK TABLES;

LOCK TABLES `Product_Characteristic` WRITE;
INSERT INTO `Product_Characteristic`(product, characteristic, type, value, measure) VALUES
  (1,1,'float','6.95','дм'),
  (1,3,'integer','5','шт'),
  (1,4,'float','0,25','м'),
  (2,1,'float','5','дм'),
  (2,3,'float','8','шт'),
  (2,4,'float','0.22','м'),
  (3,1,'float','50','дм'),
  (3,2,'boolean','true',NULL),
  (3,4,'float','100','см'),
  (4,1,'float','40','дм'),
  (4,2,'boolean','false',NULL),
  (4,4,'float','90','см'),
  (5,4,'float','30','см'),
  (5,5,'float','20','см'),
  (6,4,'float','100','см');
UNLOCK TABLES;

LOCK TABLES `Counterparty` WRITE;
INSERT INTO `Counterparty` (title,type,firstname,lastname,middlename,
                            phone,email,address,JP_type,JP_code,comment) VALUES
  ('Алі Експрес',0,'Тянь','Чан','Шань',NULL,'ali-express@ukr.net','China','хз','хз',NULL),
  ('Барахолка',1,'дядя Вася',NULL,NULL,'102',NULL,'вул. Попова 23',NULL,NULL,NULL);
UNLOCK TABLES;


LOCK TABLES `Instance` WRITE;
INSERT INTO `Instance`(product, current_count, buying_count, buying_price, currency, storage) VALUES
  (1,4,5,15000.00,'UAH','2'),
  (1,1,10,13000.00,'UAH','1'),
  (2,8,10,3000.00,'UAH','2'),
  (3,3,5,20000.00,'UAH','2'),
  (4,2,10,10000.00,'UAH','1'),
  (5,90,100,35.00,'UAH','1'),
  (6,5,10,100.00,'UAH','2');
UNLOCK TABLES;

LOCK TABLES `Transaction` WRITE;
INSERT INTO `Transaction`(total_count, type, total_price,
                          document, preparing_date, conducted_date) VALUES
  (7,0,439500.00,'накладна',NULL,'2017-04-23'),
  (7,1,267925.00,'чесне слово',NULL,'2017-04-24');
UNLOCK TABLES;

LOCK TABLES `Instance_Transaction` WRITE;
INSERT INTO `Instance_Transaction`(instance, transaction, counterparty,
                                   type, selling_count, selling_price) VALUES
  (1,1,1,0,NULL,NULL),
  (2,1,1,0,NULL,NULL),
  (3,1,1,0,NULL,NULL),
  (4,1,1,0,NULL,NULL),
  (5,1,1,0,NULL,NULL),
  (6,1,1,0,NULL,NULL),
  (7,1,1,0,NULL,NULL),
  (1,2,2,1,1,16000.00),
  (2,2,2,1,9,12000.00),
  (3,2,2,1,2,3500.00),
  (4,2,2,1,2,20000.00),
  (5,2,2,1,8,12000.00),
  (6,2,2,1,10,40.00),
  (7,2,2,1,5,105.00);
UNLOCK TABLES;

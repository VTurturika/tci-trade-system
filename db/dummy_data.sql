
--
-- Dumping data for table `Category`
--

LOCK TABLES `Category` WRITE;

INSERT INTO `Category` VALUES (1,'електроніка',0),(2,'господарство',0),(3,'телефони',1),(4,'телевізори',1),(5,'сад і город',2);

UNLOCK TABLES;

--
-- Dumping data for table `Characteristic`
--

LOCK TABLES `Characteristic` WRITE;

INSERT INTO `Characteristic` VALUES (1,'float','діагональ','дм'),(2,'boolean','тв2 ',''),(3,'integer','кількість ядер','шт'),(4,'float','висота','м,см'),(5,'float','діаметр','м,см');

UNLOCK TABLES;

--
-- Dumping data for table `Characteristic_Category`
--

LOCK TABLES `Characteristic_Category` WRITE;

INSERT INTO `Characteristic_Category` VALUES (1,3),(3,3),(4,3),(1,4),(4,4),(4,2),(5,2);

UNLOCK TABLES;

--
-- Dumping data for table `Product`
--

LOCK TABLES `Product` WRITE;

INSERT INTO `Product` VALUES (1,'Iphone',NULL,3,'12345','qwerty','23','Apple','7','3','cool',''),(2,'Xiaomi',NULL,3,'5434324','ytrewq','54','MI','Redmi 3','343','Norm',NULL),(3,'Smart TV',NULL,4,'fewrer','56564','4','Samsung','345s','78','fucking cool',NULL),(4,'Regular TV',NULL,4,'567','hthtrhtr','333','China Noname','first','so-so','FX',NULL),(5,'Горщик для квітів',NULL,5,'2324frfr','lflgflg','4','China nigers','3','',NULL,NULL),(6,'Граблі','',5,'455','544','3','Ukraine','nimbulus','2000','qwerty',NULL);

UNLOCK TABLES;

--
-- Dumping data for table `Product_Characteristic`
--

LOCK TABLES `Product_Characteristic` WRITE;

INSERT INTO `Product_Characteristic` VALUES (1,1,NULL,NULL,6.999,NULL,'м'),(1,3,NULL,5,NULL,NULL,'м'),(1,4,NULL,NULL,0.25,NULL,'м'),(2,1,NULL,NULL,5,NULL,'м'),(2,3,NULL,8,NULL,NULL,'м'),(2,4,NULL,NULL,0.25,NULL,'м'),(3,1,NULL,NULL,50,NULL,'см'),(3,2,1,NULL,NULL,NULL,'см'),(3,4,NULL,NULL,100,NULL,'см'),(4,1,NULL,NULL,40,NULL,'см'),(4,2,0,NULL,NULL,NULL,'см'),(4,4,NULL,NULL,90,NULL,'см'),(5,4,NULL,NULL,30,NULL,NULL),(5,5,NULL,NULL,20,NULL,NULL),(6,4,NULL,NULL,100,NULL,NULL);

UNLOCK TABLES;

--
-- Dumping data for table `Counterparty`
--

LOCK TABLES `Counterparty` WRITE;

INSERT INTO `Counterparty` VALUES (1,'Алі Експрес',0,'Тянь','Чан','Шань',NULL,'ali-express@ukr.net','China','хз','хз',NULL),(2,'Барахолка',0,'дядя Вася',NULL,NULL,'102',NULL,'вул. Попова 23',NULL,NULL,NULL);

UNLOCK TABLES;

--
-- Dumping data for table `Instance`
--

LOCK TABLES `Instance` WRITE;

INSERT INTO `Instance` VALUES (1,4,5,15000.00,1,'2','UAH'),(2,1,10,13000.00,1,'1','UAH'),(3,8,10,3000.00,2,'2','UAH'),(4,3,5,20000.00,3,'2','UAH'),(5,2,10,10000.00,4,'1','UAH'),(6,90,100,35.00,5,'1','UAH'),(7,5,10,100.00,6,'2','UAH');

UNLOCK TABLES;

--
-- Dumping data for table `Transaction`
--

LOCK TABLES `Transaction` WRITE;

INSERT INTO `Transaction` VALUES (1,7,0,439500.00,'накладна',NULL,'2017-04-23'),(2,7,1,267925.00,'чесне слово',NULL,'2017-04-24');

UNLOCK TABLES;

--
-- Dumping data for table `Instance_Transaction`
--

LOCK TABLES `Instance_Transaction` WRITE;

INSERT INTO `Instance_Transaction` VALUES (1,1,1,0,0,NULL),(2,1,1,0,NULL,NULL),(3,1,1,0,NULL,NULL),(4,1,1,0,NULL,NULL),(5,1,1,0,NULL,NULL),(6,1,1,0,NULL,NULL),(7,1,1,0,NULL,NULL),(1,2,2,1,1,16000.00),(2,2,2,1,9,12000.00),(3,2,2,1,2,3500.00),(4,2,2,1,2,20000.00),(5,2,2,1,8,12000.00),(6,2,2,1,10,40.00),(7,2,2,1,5,105.00);

UNLOCK TABLES;

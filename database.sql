CREATE DATABASE IF NOT EXISTS carismaleatery
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE carismaleatery;

CREATE TABLE IF NOT EXISTS Menu_Items (
  Item_ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
  Item_Name VARCHAR(100) NOT NULL,
  Price DECIMAL(10,2) NOT NULL,
  Image_Path VARCHAR(255) NOT NULL,
  stock_quantity INT NOT NULL DEFAULT 20,
  is_available TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (Item_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Cart (
  Cart_ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
  Session_ID VARCHAR(128) NOT NULL,
  Item_ID INT UNSIGNED NOT NULL,
  Quantity INT NOT NULL DEFAULT 1,
  Created_At TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Updated_At TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (Cart_ID),
  UNIQUE KEY uq_cart_session_item (Session_ID, Item_ID),
  KEY idx_cart_session (Session_ID),
  CONSTRAINT fk_cart_menu_item
    FOREIGN KEY (Item_ID) REFERENCES Menu_Items (Item_ID)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Orders (
  Order_ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
  Total_Amount DECIMAL(10,2) NOT NULL,
  Order_Date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Order_Status ENUM('Paid', 'Refunded') NOT NULL DEFAULT 'Paid',
  PRIMARY KEY (Order_ID),
  KEY idx_orders_date_status (Order_Date, Order_Status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Order_Details (
  Order_Detail_ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
  Order_ID INT UNSIGNED NOT NULL,
  Item_ID INT UNSIGNED NOT NULL,
  Quantity INT NOT NULL,
  PRIMARY KEY (Order_Detail_ID),
  KEY idx_order_details_order (Order_ID),
  CONSTRAINT fk_order_details_order
    FOREIGN KEY (Order_ID) REFERENCES Orders (Order_ID)
    ON DELETE CASCADE,
  CONSTRAINT fk_order_details_menu_item
    FOREIGN KEY (Item_ID) REFERENCES Menu_Items (Item_ID)
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Payments (
  Payment_ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
  Order_ID INT UNSIGNED NOT NULL,
  Amount DECIMAL(10,2) NOT NULL,
  Cash_Given DECIMAL(10,2) NOT NULL,
  Payment_Date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (Payment_ID),
  KEY idx_payments_order (Order_ID),
  CONSTRAINT fk_payments_order
    FOREIGN KEY (Order_ID) REFERENCES Orders (Order_ID)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Transaction_History (
  History_ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
  Order_ID INT UNSIGNED NOT NULL,
  Payment_ID INT UNSIGNED NULL,
  Action VARCHAR(50) NOT NULL,
  Details TEXT NULL,
  `Timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (History_ID),
  KEY idx_transaction_history_order (Order_ID),
  CONSTRAINT fk_transaction_history_order
    FOREIGN KEY (Order_ID) REFERENCES Orders (Order_ID)
    ON DELETE CASCADE,
  CONSTRAINT fk_transaction_history_payment
    FOREIGN KEY (Payment_ID) REFERENCES Payments (Payment_ID)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Refunds (
  Refund_ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
  Order_ID INT UNSIGNED NOT NULL,
  Payment_ID INT UNSIGNED NOT NULL,
  Amount DECIMAL(10,2) NOT NULL,
  Reason TEXT NULL,
  Refund_Date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (Refund_ID),
  KEY idx_refunds_order (Order_ID),
  CONSTRAINT fk_refunds_order
    FOREIGN KEY (Order_ID) REFERENCES Orders (Order_ID)
    ON DELETE CASCADE,
  CONSTRAINT fk_refunds_payment
    FOREIGN KEY (Payment_ID) REFERENCES Payments (Payment_ID)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS waste_log (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  item_id INT UNSIGNED NOT NULL,
  quantity_lost INT NOT NULL,
  total_loss DECIMAL(10,2) NOT NULL,
  reason TEXT NULL,
  date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_waste_log_item_date (item_id, date),
  CONSTRAINT fk_waste_log_menu_item
    FOREIGN KEY (item_id) REFERENCES Menu_Items (Item_ID)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO Menu_Items (Item_ID, Item_Name, Price, Image_Path, stock_quantity, is_available)
VALUES
  (1, 'Lechon Kawali', 70.00, 'img/lechonKawali.jpg', 20, 1),
  (2, 'Pork Adobo', 50.00, 'img/adobo.jpg', 20, 1),
  (3, 'Bibingka', 30.00, 'img/bibingka.jpg', 20, 1),
  (4, 'Crispy Pata', 90.00, 'img/crispyPata.jpg', 20, 1),
  (5, 'Leche Flan', 60.00, 'img/lecheFlan.jpg', 20, 1),
  (6, 'Lumpia', 7.00, 'img/lumpia.jpg', 20, 1),
  (7, 'Pork BBQ', 30.00, 'img/porkBBQ.jpeg', 20, 1),
  (8, 'Pork Sisig', 70.00, 'img/porksisig.jpg', 20, 1),
  (9, 'Sopas', 40.00, 'img/sopas.jpg', 20, 1)
ON DUPLICATE KEY UPDATE
  Item_Name = VALUES(Item_Name),
  Price = VALUES(Price),
  Image_Path = VALUES(Image_Path),
  is_available = VALUES(is_available);

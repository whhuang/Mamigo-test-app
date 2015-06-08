CREATE TABLE IF NOT EXISTS `google_users` (
  `google_id` decimal(21,0) NOT NULL,
  `google_name` varchar(60) NOT NULL,
  `google_fname` varchar(60),
  `google_lname` varchar(60),
  `gender` varchar(60) NOT NULL,
  `locale` varchar(60) NOT NULL,
  `google_link` varchar(60) NOT NULL,
  `google_picture_link` varchar(60) NOT NULL,
  `google_email` varchar(60) NOT NULL,
  `google_verified` varchar(60) NOT NULL,
  PRIMARY KEY (`google_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `products` (
  `product_code` INTEGER NOT NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `price` INTEGER NOT NULL);

CREATE TABLE IF NOT EXISTS `events` (
  `google_id` decimal(21,0) NOT NULL,
  `Event1` VARCHAR(1));

CREATE TABLE IF NOT EXISTS `transactions` (
  `google_id` decimal(21,0) NOT NULL,
  `token` VARCHAR(200),
  `transaction_id` VARCHAR(200),
  `timestamp` VARCHAR(200),
  `checkout_status` VARCHAR(200),
  `ack` VARCHAR(200),
  `payer_status` VARCHAR(200),
  `first_name` VARCHAR(200),
  `last_name` VARCHAR(200),
  `email_id` VARCHAR(200),
  `payer_id` VARCHAR(200),
  `country_code` VARCHAR(200),
  `curr_code` VARCHAR(200),
  `item_name` VARCHAR(200),
  `item_code` VARCHAR(200),
  `item_qty` VARCHAR(200),
  `item_amt` VARCHAR(200),
  `ship_amt` VARCHAR(200),
  `handling_amt` VARCHAR(200),
  `tax_amt` VARCHAR(200),
  `insurance_amt` VARCHAR(200),
  `total_amt` VARCHAR(200),
  `ship_discnt_amt` VARCHAR(200),
  `bill_agree_accept_status` VARCHAR(200),
  `correlation_id` VARCHAR(200),
  `version` VARCHAR(200),
  `build` VARCHAR(200),
  `insurance_offered` VARCHAR(200),
  `addr_normalize_status` VARCHAR(200),
  `error_code` VARCHAR(200));
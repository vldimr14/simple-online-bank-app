-- All SQL queries

-- Create tables

CREATE TABLE `clients` (
 `clients_id` int(11) NOT NULL AUTO_INCREMENT,
 `clients_firstName` varchar(64) NOT NULL,
 `clients_lastName` varchar(64) NOT NULL,
 `clients_passportId` varchar(12) NOT NULL,
 `clients_joinedDate` datetime DEFAULT current_timestamp(),
 `clients_email` varchar(64) NOT NULL,
 `clients_uid` varchar(32) NOT NULL,
 `clients_pwd` varchar(64) NOT NULL,
 `clients_mainAccountId` varchar(32) DEFAULT NULL,
 PRIMARY KEY (`clients_id`),
 KEY `clients_mainAccountId` (`clients_mainAccountId`),
 CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`clients_mainAccountId`) REFERENCES `accounts` (`accounts_id`) ON DELETE SET NULL
)

CREATE TABLE `transactions` (
 `transactions_id` varchar(32) NOT NULL,
 `transactions_description` varchar(255) NOT NULL,
 `transactions_date` date DEFAULT current_timestamp(),
 `transactions_amount` decimal(19,4) NOT NULL,
 `transactions_senderAccountId` varchar(32) NOT NULL,
 `transactions_senderId` int(11) NOT NULL,
 `transactions_recipientAccountId` varchar(32) NOT NULL,
 `transactions_type` varchar(32) NOT NULL,
 `transactions_senderCardNo` varchar(16) NOT NULL,
 PRIMARY KEY (`transactions_id`),
 KEY `transactions_senderAccountId` (`transactions_senderAccountId`),
 KEY `transactions_recipientAccountId` (`transactions_recipientAccountId`),
 KEY `senderId_fk` (`transactions_senderId`),
 CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`transactions_senderId`) REFERENCES `clients` (`clients_id`),
 CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`transactions_senderAccountId`) REFERENCES `accounts` (`accounts_id`),
 CONSTRAINT `transactions_ibfk_4` FOREIGN KEY (`transactions_recipientAccountId`) REFERENCES `accounts` (`accounts_id`)
) 

CREATE TABLE `accounts` (
 `accounts_id` varchar(32) NOT NULL,
 `accounts_effectiveDate` datetime DEFAULT current_timestamp(),
 `accounts_currency` varchar(3) NOT NULL,
 `accounts_balance` decimal(19,4) NOT NULL,
 `clients_id` int(11) NOT NULL,
 PRIMARY KEY (`accounts_id`),
 KEY `clients_id` (`clients_id`),
 CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`clients_id`) REFERENCES `clients` (`clients_id`)
) 

-- SQL main queries

-- Refresh client information
SELECT * FROM clients WHERE clients_id = :id;

-- Get account information
SELECT * FROM accounts WHERE clients_id = :clients_id AND accounts_id = :accountNo;

-- Check if account already exists 

SELECT accounts_id FROM accounts WHERE accounts_id = :accountNo;

-- Check account currency

SELECT accounts_currency FROM accounts WHERE accounts_id = :accountNo;

-- Check if the user has the account with the same currency

SELECT accounts_id FROM accounts WHERE clients_id = :clients_id AND accounts_currency = :currency;

-- Add new account to database

INSERT INTO accounts (accounts_id, accounts_balance, accounts_currency, clients_id) VALUES (:accountNo, :balance, :currency, :clients_id);

-- Create a bond between account and user

UPDATE clients SET clients_mainAccountId = :accountNo WHERE clients_id = :clients_id;

-- Get transaction information from database.
-- Using left join to get accounts currency.
SELECT transactions_id, transactions_date, transactions_description, transactions_amount, accounts.accounts_currency, transactions_senderAccountId, transactions_recipientAccountId, transactions_senderId FROM transactions 
INNER JOIN accounts ON transactions.transactions_senderId = accounts.clients_id 
WHERE transactions_senderAccountId = :accountId OR transactions_recipientAccountId = :accountId 
ORDER BY transactions_date DESC;

-- Check if transaction exists

SELECT transactions_id FROM transactions WHERE transactions_id = :transactionNo;

-- Make a transfer

INSERT INTO transactions (transactions_id, transactions_description, transactions_amount, transactions_senderAccountId, transactions_senderId, transactions_recipientAccountId, transactions_type, transactions_senderCardNo) VALUES (:transactionsNo, :transactions_description, :transactions_amount, :transactions_senderAccountNo, :transactions_senderId, :transactions_recipientAccountNo, :transactions_type, :transactions_senderCardNo);

-- Update sender account after transfer operation.

UPDATE accounts SET accounts_balance = accounts_balance - :amount WHERE accounts_id = :senderAccountNo

-- Update recipient account after transfer operation.

UPDATE accounts SET accounts_balance = accounts_balance + :amount WHERE accounts_id = :recipientAccountNo




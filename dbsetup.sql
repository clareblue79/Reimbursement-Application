-- CS 304 Final Project: Wellesley Reimbursement Website      
-- Team name: CHiLY               
-- Programmers: Clare Lee, Hanae Yaskawa                      
-- File: dbsetup.sql              
-- Description: Batch file for queries to set up databases. On delete cascade for all tables.

USE chily_db;

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS bursar_approval;
DROP TABLE IF EXISTS bookie_approval;
DROP TABLE IF EXISTS attendees;
DROP TABLE IF EXISTS receipts;
DROP TABLE IF EXISTS treasurers;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS forms;
DROP TABLE IF EXISTS orgs;
DROP TABLE IF EXISTS accts;

-- Accounts table: stores account information when the user registers. We kept the varchar lenght
-- for username, password, and address long since we cannot assume people will have short inputs for those fields
CREATE TABLE accts (
       uid INT auto_increment not null PRIMARY KEY,
       username VARCHAR (200) not null,
       password VARCHAR (200) not null,
       fullname VARCHAR(30) not null,
       bnumber CHAR(9) not null,
       address VARCHAR (100) not null,
       acct_type ENUM('Student','Employee','Vendor','Treasurer','Bookkeeper','Bursar') not null
       )
       ENGINE = InnoDB;

-- Treasurers tables: keeps track of which treasurers belong to which org 
CREATE TABLE treasurers (
       uid INT,
       orgid INT,
       FOREIGN KEY (orgid) references accts(uid) ON DELETE CASCADE
       )
       ENGINE = InnoDB;

-- Orgs table: stores organization's information and which bookie is in charge of the org
CREATE TABLE orgs (
       orgid INT auto_increment not null PRIMARY KEY,
       orgname VARCHAR (20) not null,
       bookieid INT,
       sofc CHAR (3) not null,
       profits CHAR (3) not null,
       clce CHAR (3) not null,
       FOREIGN KEY (bookieid) references accts(uid) ON DELETE CASCADE
       )
       ENGINE = InnoDB;

-- Forms table: stores all the reimbursement form information 
CREATE TABLE forms (
       fid INT auto_increment not null PRIMARY KEY,
       uid INT,
       orgid INT,
       date_prepared DATE,
       reimb_id INT,
       purpose VARCHAR (400),
       sofc_amnt FLOAT,
       sofc_loc VARCHAR (40),
       profit_amnt FLOAT,
       profit_loc VARCHAR (40),
       clce_amnt FLOAT,
       clce_loc VARCHAR (40),
       ttl_amnt FLOAT,
       spec_inst ENUM('send_check','email_check'),
       email VARCHAR (20),
       status ENUM('saved','submitted'),
       FOREIGN KEY (uid) references accts(uid) ON DELETE CASCADE,
       FOREIGN KEY (reimb_id) references accts(uid) ON DELETE CASCADE,
       FOREIGN KEY (orgid) references orgs(orgid) ON DELETE CASCADE
       )
       ENGINE = InnoDB;

-- Events table: stores event information 
CREATE TABLE events (
       eid INT auto_increment not null PRIMARY KEY,
       ename VARCHAR(20),
       fid INT,
       orgid INT,
       edate DATE,
       num_attendees INT,
       category VARCHAR (10),
       amnt FLOAT,
       fundsrc ENUM('Profits','SOFC','GP','CLCE'),
       FOREIGN KEY (fid) references forms(fid) ON DELETE CASCADE,
       FOREIGN KEY (orgid) references orgs(orgid) ON DELETE CASCADE
       )
       ENGINE = InnoDB;

-- Receipts table: stores reciept information and its jpeg image
CREATE TABLE receipts (
       rid INT auto_increment not null PRIMARY KEY,
       fid INT,
       rfile VARCHAR (40),
       FOREIGN KEY (fid) references forms(fid) ON DELETE CASCADE
       )
       ENGINE = InnoDB;

-- Attendees table: stores attendence information for events
CREATE TABLE attendees (
       aid INT auto_increment not null PRIMARY KEY,
       fid INT,
       afile VARCHAR (40),
       FOREIGN KEY (fid) references forms(fid) ON DELETE CASCADE
       )
       ENGINE = InnoDB;

-- Bookie approval table: keeps track of the status of the submitted forms that 
-- are under bookkeeper's review
CREATE TABLE bookie_approval(
       fid INT,
       bookieid INT,
       status ENUM('not_checked','approved','rejected'),
       approved_date DATE default null,
       comment VARCHAR(1000),
       FOREIGN KEY (fid) references forms(fid) ON DELETE CASCADE,
       FOREIGN KEY (bookieid) references accts(uid) ON DELETE CASCADE
       )
       ENGINE = InnoDB;

-- Student Bursar approval table: keeps track of the status of all submitted forms
-- approved by the bookkeepers
CREATE TABLE bursar_approval(
       fid INT,
       status ENUM('not_checked','approved','rejected'),
       approved_date DATE default null,
       comment VARCHAR(1000),	 
       FOREIGN KEY (fid) references forms(fid) ON DELETE CASCADE
       )
       ENGINE = InnoDB;



-- Inserting test cases with hashed password for "12345678"
INSERT INTO accts(username, password, fullname, bnumber, address, acct_type) VALUES ("bookie@wellesley.edu",'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f' ,"Bookie Test1", "B11111111", "Wellesley College", "Bookkeeper");

INSERT INTO accts(username, password, fullname, bnumber, address, acct_type) VALUES ("bookie2@wellesley.edu",'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f' ,"Bookie Test1", "B22222222", "Wellesley College", "Bookkeeper");

INSERT INTO accts(username, password, fullname, bnumber, address, acct_type) VALUES ("bookie3@wellesley.edu",'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f' ,"Bookie Test1", "B33333333", "Wellesley College", "Bookkeeper");

INSERT INTO accts(username, password, fullname, bnumber, address, acct_type) VALUES ("wendy@wellesley.edu",'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f' ,"Wendy Wellesley", "B44444444", "Wellesley College", "Student");

INSERT INTO accts(username, password, fullname, bnumber, address, acct_type) VALUES ("treasurer@wellesley.edu",'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f' ,"Treasurer Test1", "B55555555", "Wellesley College", "Treasurer");

INSERT INTO accts(username, password, fullname, bnumber, address, acct_type) VALUES ("employee@employee.com", 'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f', "Employee Name", "B66666666", "555 Employee Address", "Employee");

INSERT INTO accts(username, password, fullname, bnumber, address, acct_type) VALUES ("vendor@vendor.com", 'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f', "Vendor Name", "B77777777", "666 Vendor Address", "Vendor");

INSERT INTO accts(username, password, fullname, bnumber, address, acct_type) VALUES ("clee19@wellesley.edu", 'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f', "Bursar Lee", "B88888888", "888 Bursar Address", "Bursar");


INSERT INTO orgs(orgname, bookieid, sofc, profits, clce) VALUES ("Japan Club",1,"111","111","111");
INSERT INTO orgs(orgname, bookieid, sofc, profits, clce) VALUES	("Crew Team",2,"222","222","222");
INSERT INTO orgs(orgname, bookieid, sofc, profits, clce) VALUES	("CS Club",3,"333","333","333");





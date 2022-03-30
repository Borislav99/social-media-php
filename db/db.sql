DROP DATABASE social;
CREATE DATABASE social;
USE social;
CREATE TABLE users (
  id INT(11) NOT NULL UNIQUE AUTO_INCREMENT,
  first_name VARCHAR(25),
  last_name VARCHAR(25),
  username VARCHAR(100) UNIQUE PRIMARY KEY,
  email VARCHAR(100),
  password VARCHAR(255),
  signup_date DATETIME,
  profile_pic VARCHAR(255),
  num_posts INT(11),
  num_likes INT(11),
  user_closed VARCHAR(3),
  friend_array TEXT
);
CREATE TABLE posts (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  body TEXT,
  added_by VARCHAR(60),
  user_to VARCHAR(60),
  date_added DATETIME,
  user_closed VARCHAR(3),
  deleted VARCHAR(3),
  likes INT(11),
  FOREIGN KEY(added_by) REFERENCES users(username)
);
CREATE TABLE notifications (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_to VARCHAR(50) NOT NULL,
  user_from VARCHAR(50) NOT NULL,
  message TEXT,
  link VARCHAR(100),
  datetime DATETIME NOT NULL,
  opened VARCHAR(3),
  viewed VARCHAR(3),
  FOREIGN KEY(user_to) REFERENCES users(username),
  FOREIGN KEY(user_from) REFERENCES users(username)
);
CREATE TABLE messages(
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_to VARCHAR(50),
  user_from VARCHAR(50),
  body TEXT,
  date DATETIME,
  opened VARCHAR(3),
  viewed VARCHAR(3),
  deleted VARCHAR(3),
  FOREIGN KEY(user_to) REFERENCES users(username),
  FOREIGN KEY(user_from) REFERENCES users(username)
);
CREATE TABLE likes(
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60),
  post_id INT(11) NOT NULL,
  FOREIGN KEY(post_id) REFERENCES posts(id),
  FOREIGN KEY(username) REFERENCES users(username)
);
CREATE TABLE friend_requests (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_to VARCHAR(50),
  user_from VARCHAR(50),
  FOREIGN KEY(user_to) REFERENCES users(username),
  FOREIGN KEY(user_from) REFERENCES users(username)
);
CREATE TABLE comments (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  post_body TEXT,
  posted_by VARCHAR(60),
  posted_to VARCHAR(60),
  date_added DATETIME,
  removed VARCHAR(3),
  post_id INT(11) NOT NULL,
  FOREIGN KEY(post_id) REFERENCES posts(id),
  FOREIGN KEY(posted_by) REFERENCES users(username),
  FOREIGN KEY(posted_to) REFERENCES users(username)
);
SHOW TABLES;
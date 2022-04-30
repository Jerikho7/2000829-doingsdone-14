CREATE DATABASE doingsdone
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;

USE doingsdone;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR(255) NOT NULL UNIQUE,
  name VARCHAR(128) NOT NULL,
  password CHAR(64) NOT NULL
);

CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL UNIQUE,
  user_id INT,
  FOREIGN KEY (user_id) REFERENCES users (id)
);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status INT DEFAULT 0,
    name VARCHAR(255) NOT NULL,
    file VARCHAR(255),
    deadline_at TIMESTAMP NOT NULL,
    user_id INT,
    project_id INT,
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (project_id) REFERENCES projects (id)
);

CREATE INDEX u_email ON users (email);
CREATE INDEX p_name ON projects (name);


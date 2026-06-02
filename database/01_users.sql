CREATE TABLE Users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       name VARCHAR(255),
                       username VARCHAR(255),
                       password VARCHAR(255),
                       role VARCHAR(50) NOT NULL DEFAULT 'user'
);

-- CREATE  TABLE Users(
--                        id integer primary key Autoincrement,
--                        name text,
--                        username text,
--                        password text,
--                        role TEXT NOT NULL DEFAULT 'user'
-- )
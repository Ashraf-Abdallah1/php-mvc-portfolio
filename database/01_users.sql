CREATE TABLE Users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       name VARCHAR(255),
                       username VARCHAR(255),
                       password VARCHAR(255),
                       role VARCHAR(50) NOT NULL DEFAULT 'user'
);

insert into Users (id, name, username, password, role) VALUES
                                                           (1,'admin', 'admin', '$2y$10$qQbGwBGYGsuHKkfhpg1hOelfSGH4D6MeWAgJbxJHxhikrXri6a/R2', 'admin' ),
                                                           (2,'guest', 'guest', '$2y$10$3h79DICAJN8fE1N9y6cYY.g03v.0n/OD5RuXR/noLnSyuAkdrmp2K', 'user' )
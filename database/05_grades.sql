CREATE TABLE IF NOT EXISTS Grades (
                                      id INT AUTO_INCREMENT PRIMARY KEY,
                                      user_id INTEGER NOT NULL,
                                      course TEXT NOT NULL,
                                      grade REAL NOT NULL,
                                      FOREIGN KEY (user_id) REFERENCES Users(id)
    );

INSERT INTO Grades (user_id, course, grade) VALUES
                                                (1, 'Java', 8.5),
                                                (1, 'PHP', 7.8),
                                                (1, 'Databases', 7.2),
                                                (1, 'Web Development', 8.0),
                                                (1, 'Project Scalda', 9.0);
CREATE TABLE IF NOT EXISTS Dashboards (
                                         id INT AUTO_INCREMENT PRIMARY KEY,
                                         user_id INT NOT NULL,
                                         earned_credits INTEGER DEFAULT 0,
                                         total_credits INTEGER DEFAULT 60,
                                         FOREIGN KEY (user_id) REFERENCES Users(id)
    );

INSERT INTO Dashboards (user_id, earned_credits, total_credits)
VALUES (1, 45, 60);
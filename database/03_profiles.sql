CREATE TABLE Profiles (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          user_id INT NOT NULL,
                          name VARCHAR(255),
                          bio TEXT,
                          skills VARCHAR(255),
                          experience TEXT,
                          projects TEXT,
                          contact VARCHAR(255),
                          FOREIGN KEY (user_id) REFERENCES Users(id)
);

INSERT INTO Profiles (
    user_id,
    name,
    bio,
    skills,
    experience,
    projects,
    contact
) VALUES (
             1,
             'Ashraf Abdallah',
             'Ik ben een gemotiveerde Software Developer met een afgeronde MBO Niveau 4 Software Development opleiding aan Scalda.'
             ' Momenteel studeer ik HBO-ICT aan de HZ University of Applied Sciences.'
             ' Naast mijn studie werk ik als Assistent Manager in een kledingwinkel.'
             ' Ik combineer techniek, teamwork en praktijkervaring om professionele oplossingen te bouwen.',
             'PHP, HTML, CSS, Java, JavaScript, Docker, Symfony, MySQL',
             'Veel ervaring met Java. Ik heb met een team een afwezigheidssysteem gebouwd voor Scalda waarbij studenten kunnen in- en uitchecken via QR-codes.'
             ' Docenten krijgen een volledig dashboard met statistieken en controle. Ook heb ik gewerkt aan een digitaal vinger-opsteek systeem met een plattegrond van het lokaal.'
             ' Daarnaast heb ik meegewerkt aan ZironLine, een project dat wereldwijd door MediaMarkt wordt gebruikt.',
             'Afwezigheidssysteem met QR-check-in; Virtueel vinger-opsteek systeem; ZironLine (MediaMarkt); Diverse schoolprojecten in Java, PHP en Symfony',
             'abda0002@hz.nl'
         );
CREATE  TABLE Blogs(
                       id integer primary key Autoincrement,
                       title text,
                       content text,
                       date integer
);

insert into Blogs (title, content, date) VALUES ("test", "test", 1010),
                                                ("test1", "test1", 10110),
                                                ("test2", "test2", 10110)
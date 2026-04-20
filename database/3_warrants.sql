create table warrants(
                       id integer primary key Autoincrement,
                       accused_name text,
                       stage integer,
                       level integer,
                       agent_id integer,
                    FOREIGN KEY (agent_id) REFERENCES agents(id)
                     on delete set null
                     on update cascade
);

insert into warrants (accused_name,stage,level) values ('warrats',1,1),
                                                         ('warrats2',1,1),
                                                         ('warrats3',1,1)
create table agents(
                        id integer primary key Autoincrement,
                        name text,
                        stage integer,
                        level integer,
                        broker_id integer,
    FOREIGN KEY (broker_id) REFERENCES brokers(id)
                   on delete  set null
                   on update cascade
);

insert into agents (name,stage,level,broker_id ) values ('Agent1',1,1,1),
                                  ('Agent2',1,1,1),
                                  ('Agent2',1,1,1)
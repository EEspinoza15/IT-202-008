ALTER TABLE Users
    ADD COLUMN username varchar(60) default '',
    ADD UNIQUE (username);

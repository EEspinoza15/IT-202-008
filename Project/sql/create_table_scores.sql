CREATE TABLE scores
(
    id      int auto_increment,
    user_id int,
    score   int,
    primary key (id),
    foreign key (user_id) references Users (id)
)

CREATE TABLE Users (
    id INTEGER PRIMARY KEY NOT NULL,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    code VARCHAR(255)
);

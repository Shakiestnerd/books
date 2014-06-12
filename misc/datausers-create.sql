CREATE TABLE datausers ( 
    id       INTEGER         PRIMARY KEY AUTOINCREMENT
                             NOT NULL,
    username VARCHAR( 255 )  NOT NULL
                             COLLATE 'NOCASE',
    password CHAR( 64 )      NOT NULL,
    salt     CHAR( 16 ),
    email    VARCHAR( 255 )  NOT NULL 
);

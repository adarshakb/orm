# Optional database creating line to be executed

CREATE DATABASE parent_newsletter;

# ADMIN ACCESS TABLE LIST
# ENSURE THAT ATLEAST ONE ADMIN STAYS IN THIS.
CREATE TABLE AT_ADMIN (
	USERNAME VARCHAR(75) PRIMARY KEY,
	PASSWORD VARCHAR(75) NOT NULL
);

# POSTS TABLE TO CONTAIN THE DATA
CREATE TABLE AT_POSTS (
	ID INTEGER PRIMARY KEY AUTO_INCREMENT, #UNIQUE ID FOR THE POST
	USER VARCHAR(75) DEFAULT NULL, #DEFAULT CAN BE NULL TO GIVE A POSSIBLE CHOICE OF ANONYMUS POSTER
	TITLE VARCHAR(255) NOT NULL, #THE POST'S TITLE
	POST TEXT, #THE PLACE TO CONTAIN THE ACTUAL HTML OF THE POST
	STATUS SMALLINT DEFAULT 0, #0- DRAFT, 1- PUBLISHED
	
	CREATED TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, #MYSQL DOESNT ALLOW CURRENT_TIMESTAMP FOR 2 COLUMS, TAKE CARE IF THIS IN PHP
	UPDATED TIMESTAMP,
	FIRST_PUBLISHED TIMESTAMP,
	PUBLISHED TIMESTAMP,

	FOREIGN KEY(USER) REFERENCES AT_ADMIN(USERNAME),

	FULLTEXT (TITLE,POST),
	INDEX (TITLE(85),POST(247))
)ENGINE=MyISAM CHARACTER SET=utf8;

# Insert the first row into database for admin

INSERT INTO  `AT_ADMIN` (
`USERNAME` ,
`PASSWORD`
)
VALUES (
'root',  '63a9f0ea7bb98050796b649e85481845'
);

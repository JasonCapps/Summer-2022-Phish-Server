#Database for the Go-Phish WebApp

#Destroy and Recreate Database

DROP table pond.phish;
DROP table pond.grade;
DROP table pond.victim;
DROP table pond.payload;
DROP table pond.logs;
DROP table pond.user;

DROP database pond;
CREATE database pond;

#This section creates the Tables in the Database
#
#
#TABLE PHISH
#
#The "phish" table stores the primary data for the phish. This is the core
#data that will be used for the dashboard/summary of the phish
#This is the identifying information for the phish
#Students will input this data into their submission
#
#id = the Primary key. This is how the database tables relate to eachother
#country = The country who submitted the phish
#name = The common name of the attack/operation such as "Rolling Steel"
#submit_date = The datetime the phish was submitted
#
CREATE table pond.phish
(id INT NOT NULL AUTO_INCREMENT,
country VARCHAR(14) NOT NULL,
name VARCHAR(50) NOT NULL,
submit_date DATETIME DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (id));

#TABLE grade
#
#The "grade" table stores the grading information
#The grade information is what the Professor
#Responds with after reviewing the Phish
#
#id = the Primary key
#grade = The Letter grade given by the Professor. This can be any single letter
#But most like A,B,C,D,E,F but it could be U,W,O or X,P
#comment = The comment given by the Professor. Capped at 500 Words because it most
#likely wont require an Essay in response
#approval = The approval status. The expected values are A for approved, D for Denied or P for pending
#
#
CREATE table pond.grade
(id INT NOT NULL AUTO_INCREMENT,
grade VARCHAR(10),
comment VARCHAR(500),
approval VARCHAR(10),
PRIMARY KEY (id));

#TABLE VICTIM
#
#The "victim" table stores information about the victim/target of the phish. Each
#Phish has been designed for 1 victim per phish. If you want to hit multiple targets
#Submit more Phish. The student countries would submit this data as well. This is the "shipping address"
#
#
#id = the Primary Key
#country = the targeted country. Capped at 14 characters because no country in use has more the 14 chars in its name
#hostname = The name of the host to be targeted.
#username = The name of the user that will be "overly curious" when it comes to their email attachments
#message = the text or whatever the victim user "read" in their email. Be creative, have fun.
#
#
CREATE table pond.victim
(id INT NOT NULL AUTO_INCREMENT,
country VARCHAR(14) NOT NULL,
hostname VARCHAR(100) NOT NULL,
os VARCHAR(8) NOT NULL,
username VARCHAR(50) NOT NULL,
message VARCHAR(144) NOT NULL,
PRIMARY KEY (id));

#TABLE PAYLOAD
#
#The "payload" table stores the information regarding the actual payload.
#This is the executable/file/command to be run by ansible.
#
#id = The Primary Key
#type = This is a single character denoting the type. F for File (.hta, .odt, .py) 
#E for an Executable (.exe, .elf , .sh) and C for command (useradd, ssh, rm -rf)
#payload = the UUID for the payload. the UUID datatype isnt working, however UUIDs are 32 characters
#so tomato tomato. However this is something that could be re-factored at a later data
#
CREATE table pond.payload
(id INT NOT NULL AUTO_INCREMENT,
type VARCHAR(1) NOT NULL,
directory VARCHAR(100) NOT NULL,
file VARCHAR(100) NOT NULL,
command VARCHAR(200),
PRIMARY KEY (id));

#TABLE LOGS
#
#The "logs" table has references to the log files. 
#
#id = Primary keys
#logs = The UUID of the logs. UUID datatype was not working. VARCHAR(32) can store a 32 character string (aka UUID)
#This could/should be refactored
#
CREATE table pond.logs
(id INT NOT NULL AUTO_INCREMENT,
logs VARCHAR(40),
PRIMARY KEY (id));

CREATE table pond.user
(id int not null AUTO_INCREMENT,
account_id INT not null,
username varchar(20) not null,
password_hash varchar(200) not null,
PRIMARY KEY (id));

INSERT INTO pond.user (id, account_id, username, password_hash) VALUES ('1', '1', 'moleary', SHA2('fishfearme1!',256));

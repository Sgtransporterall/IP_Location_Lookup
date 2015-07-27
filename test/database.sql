create table ip(
	IP_Start 	varchar(15),
	IP_End 		varchar(15),
	Location_ID1 tinytext,
	Location_ID2 tinytext,
	Country_Code tinytext,
	Country_Name tinytext
);

LOAD DATA LOCAL INFILE 'C:/Users/Zijian/Desktop/GeoIPCountryCSV/GeoIPCountryWhois.csv' 
INTO TABLE ip 
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n';



SELECT ip, SUBSTRING_INDEX(ip,'.',1) AS part1, 
SUBSTRING_INDEX(SUBSTRING_INDEX(ip,'.',2),'.',-1) AS part2, 
SUBSTRING_INDEX(SUBSTRING_INDEX(ip,'.',-2),'.',1) AS part3, 
SUBSTRING_INDEX(ip,'.',-1) AS part4  FROM log_file;

create table ipp(
	part1 tinyint unsigned,
	part2 tinyint unsigned,
	part3 tinyint unsigned,
	part4 tinyint unsigned
);


INSERT INTO ipp (part1,part2,part3, part4,country) 
SELECT SUBSTRING_INDEX(ip_address1,'.',1), 
SUBSTRING_INDEX(SUBSTRING_INDEX(ip_address1,'.',2),'.',-1),
SUBSTRING_INDEX(SUBSTRING_INDEX(ip_address1,'.',-2),'.',1),
SUBSTRING_INDEX(ip_address1,'.',-1),
country_name
FROM ip;
*/



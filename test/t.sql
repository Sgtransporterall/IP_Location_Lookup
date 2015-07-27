create table ipp(
	part1 tinyint unsigned,
	part2 tinyint unsigned,
	part3 tinyint unsigned,
	part4 tinyint unsigned,
	country tinytext
);


INSERT INTO ipp (part1,part2,part3, part4,country) 
SELECT SUBSTRING_INDEX(ip_address1,'.',1), 
SUBSTRING_INDEX(SUBSTRING_INDEX(ip_address1,'.',2),'.',-1),
SUBSTRING_INDEX(SUBSTRING_INDEX(ip_address1,'.',-2),'.',1),
SUBSTRING_INDEX(ip_address1,'.',-1),
country_name
FROM ip;

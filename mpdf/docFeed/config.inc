<?php

$hostname = 'bottom-up-dev.c8lq1wttwtce.ap-southeast-1.rds.amazonaws.com:3306';        // Your MySQL hostname. Usualy named as 'localhost', so you're NOT necessary to change this even this script has already online on the internet.
$dbname   = 'doctor_feed'; // Your database name.
$username = 'admin';             // Your database username.
$password = '5tgbvfr4';                 // Your database password. If your database has no password, leave it empty.

// Let's connect to host
mysql_connect($hostname, $username, $password) or DIE('Connection to host is failed, perhaps the service is down!');
// Select the database
mysql_select_db($dbname) or DIE('Database name is not available!');
mysql_query('SET CHARACTER SET utf8');
mysql_query("SET SESSION collation_connection ='utf8_general_ci'") or die (mysql_error());




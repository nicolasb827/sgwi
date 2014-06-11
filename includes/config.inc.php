<?php

/***************************************************
SQLgrey Web Interface
Filename:	config.inc.php
Purpose: 	Configuration database and options
Version: 	1.1.5
****************************************************/

/* Database settings */
$db_db		= "sqlgrey";
$db_hostname	= "localhost";
$db_user	= "sqlgrey";
$db_pass	= "TA7NtShy3cw2HmBT";
$db_type	= "mysql";	// mysql or pg (pg=postgress)

/* Set close_btn to 'yes' to enable the close button in index.php (main menu)
   the button action = ../ which could be a security issue
   default = no
*/
$close_btn	= "yes";

/* Set no_millisecs to 'no' if your server's dbase shows milliseconds
   and you do want these to be displayed - this will take two lines per entry.
   Also set this to 'no' if you encounter problems with displaying the timestamps
   ('no' used to be the default and leaves the date format untouched).
   When set to 'yes' timestamps will be formatted as 'yyyy-mm-dd hh:mm:ss'
   which doubles the amount of visible entries.
   default = yes
*/
$no_millisecs	= "yes";
$locale = "fr";
?>

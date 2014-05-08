<?php

/***************************************************
SQLgrey Web Interface
Filename:	connect.inc.php
Purpose: 	Functions for connect.php (greylist)
Version: 	1.1.6
****************************************************/

function forget_entry($sendername, $senderdomain, $src, $rcpt) {
	global $deleted;
	$query = "DELETE FROM connect WHERE sender_name='".addslashes($sendername)."' AND sender_domain='".addslashes($senderdomain)."' AND src='".addslashes($src)."' AND rcpt='".addslashes($rcpt)."'";
	do_query($query);
	$deleted .= '<br />'.$sendername.'@'.$senderdomain.' ['.$src.'] for '.$rcpt.' deleted.';
}

function move_entry($sendername, $senderdomain, $src, $rcpt) {
	global $moved;
	$query = "SELECT first_seen FROM connect WHERE sender_name='".addslashes($sendername)."' AND sender_domain='".addslashes($senderdomain)."' AND src='".addslashes($src)."' AND rcpt='".addslashes($rcpt)."'";
	$result = do_query($query);
	$line = fetch_row($result);
	# add to 'from_awl'
	$query = "INSERT INTO from_awl(sender_name, sender_domain, src, first_seen, last_seen) VALUES('".
			addslashes($sendername)."', '".
			addslashes($senderdomain)."', '".
			addslashes($src)."', '".
			$line["first_seen"]."', '".
			$line["first_seen"]."')";
	do_query($query);
	# and remove from 'connect'
	$query = "DELETE FROM connect WHERE sender_name='".addslashes($sendername)."' AND sender_domain='".addslashes($senderdomain)."' AND src='".addslashes($src)."' AND rcpt='".addslashes($rcpt)."'";
	do_query($query);
	$moved .= '<br />'.$sendername.'@'.$senderdomain.' ['.$src.'] for '.$rcpt.' moved to whitelist.';
}

function del_older_than($year, $month, $day, $hour, $minute, $seconds, $err) {
	global $warning, $message;
	if ($err) {
		$warning = "Aborted: invalid date.";
	} else {
		$nicedate = $year.'-'.substr("00".$month, -2, 2).'-'.substr("00".$day, -2, 2).' '.substr("00".$hour, -2, 2).':'.substr("00".$minute, -2, 2).':'.substr("00".$seconds, -2, 2);
		$query = "DELETE FROM connect WHERE first_seen < ".$year.substr("00".$month, -2, 2).substr("00".$day, -2, 2).substr("00".$hour, -2, 2).substr("00".$minute, -2, 2).substr("00".$seconds, -2, 2);
		do_query($query);
		$message = 'Entries older than '.$nicedate.' deleted.';
		$warning = "<br />Sorting set to &#39;Seen at&#39; (ascending).";
	}

}

?>
<?php

/***************************************************
SQLgrey Web Interface
Filename:	awl.inc.php
Purpose: 	Functions for awl.php (whitelists)
Version: 	1.1.6
****************************************************/

function add_sender($mode, $sendername, $senderdomain, $src) {
	global $added;
	if ($mode == "email") {
		if ($sendername == '' || $senderdomain == '' || $src == '') {
			$added = "<br />WARNING: Insufficient data - nothing was added !";
		} else {
			$query = "INSERT INTO from_awl(sender_name, sender_domain, src, first_seen, last_seen)
				  VALUES('".addslashes($sendername)."', '".addslashes($senderdomain)."', '".addslashes($src)."', now(), now())";
			$added = "<br />E-mail address ".$sendername."@".$senderdomain." (".$src.") added.";
			do_query($query);
		}
	} else {
		if ($senderdomain == '' || $src == '') {
			$added = "<br />WARNING: Insufficient data - nothing was added!";
		} else {
			$query = "INSERT INTO domain_awl(sender_domain, src, first_seen, last_seen)
				  VALUES('".addslashes($senderdomain)."', '".addslashes($src)."', now(), now())";
			$added = "<br />Domain ".$senderdomain." (".$src.") added.";
			do_query($query);
		}
	}
}

function delete_undef($mode) {
	global $message;
	if ($mode == "email") {
		$query_cnt = "SELECT COUNT(*) AS count FROM from_awl WHERE sender_name='-undef-' AND sender_domain='-undef-'";
		$query = "DELETE FROM from_awl WHERE sender_name='-undef-' AND sender_domain='-undef-'";
	} else {
		$query_cnt = "SELECT COUNT(*) AS count FROM domain_awl WHERE sender_domain='-undef-'";
		$query = "DELETE FROM domain_awl WHERE sender_domain='-undef-'";
	}
	$result = do_query($query_cnt);
	$n = fetch_row($result);
	if ($n["count"] > 0) {
		do_query($query);
		$message = '<br />-undef- entries ('.$n["count"].') deleted.';
	} else {
		$message = "<br />No -undef- entries found - nothing was deleted.";
	}
}

function delete_entry($mode, $sendername, $senderdomain, $src) {
	global $deleted;
	if ($mode == "email") {
		$query = "DELETE FROM from_awl WHERE sender_name='".addslashes($sendername)."' AND sender_domain='".addslashes($senderdomain)."' AND src='".addslashes($src)."'";
		$deleted .= "<br />".$sendername."@".$senderdomain." (".$src.") deleted.";
	} else {
		$query = "DELETE FROM domain_awl WHERE sender_domain='".addslashes($senderdomain)."' AND src='".addslashes($src)."'";
		$deleted .= "<br />".$senderdomain." (".$src.") deleted.";
	}
	do_query($query);
}

?>
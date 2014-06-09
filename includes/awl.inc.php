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
			$added = "<br />"._("WARNING: Insufficient data - nothing was added !");
		} else {
			$query = "INSERT INTO from_awl(sender_name, sender_domain, src, first_seen, last_seen)
				  VALUES('".addslashes($sendername)."', '".addslashes($senderdomain)."', '".addslashes($src)."', now(), now())";
			$added = "<br />".printf(_("E-mail address %s (%s) added."), $sendername."@".$senderdomain, $src);
			do_query($query);
		}
	} else {
		if ($senderdomain == '' || $src == '') {
			$added = "<br />"._("WARNING: Insufficient data - nothing was added !");
		} else {
			$query = "INSERT INTO domain_awl(sender_domain, src, first_seen, last_seen)
				  VALUES('".addslashes($senderdomain)."', '".addslashes($src)."', now(), now())";
			// TODO commit
			$added = "<br />".printf(_("Domain %s (%s) added."), $senderdomain, $src);
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
		// TODO commit
		$message = '<br />'.printf(_("-undef- entries (%d) deleted."), $n["count"]);
	} else {
		$message = "<br />"._("No -undef- entries found - nothing was deleted.");
	}
}

function delete_entry($mode, $sendername, $senderdomain, $src) {
	global $deleted;
	if ($mode == "email") {
		$query = "DELETE FROM from_awl WHERE sender_name='".addslashes($sendername)."' AND sender_domain='".addslashes($senderdomain)."' AND src='".addslashes($src)."'";
		$deleted .= "<br />".printf(_("E-mail address %s (%s) deleted."), $sendername."@".$senderdomain, $src);
	} else {
		$query = "DELETE FROM domain_awl WHERE sender_domain='".addslashes($senderdomain)."' AND src='".addslashes($src)."'";
		$deleted .= "<br />".printf(_("Domain %s (%s) deleted."), $senderdomain, $src);
	}
	do_query($query);
}

?>
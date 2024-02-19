<?php
/**
 * Exclude Usergroups from Board-Statistics
 * Copyright 2024 ZnapShot
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

if(!defined("PLUGINLIBRARY"))
{
    define("PLUGINLIBRARY", MYBB_ROOT."inc/plugins/pluginlibrary.php");
}

// basic info for plugin manager
function zdexcludegroupfromstat_info()
{
	global $lang;
	
	return array(
		"name"				=> "ZD Exclude Usergroup from user count",
		"description"		=> "Excludes user from specific usergroup from user count in board statistics.",
		"website"			=> "http://www.znapdev.de",
		"author"			=> "ZnapShot",
		"authorsite"		=> "http://www.znapdev.de",
		"version"			=> "0.0.1",
		"codename"			=> "zdexcludegroupfromstat",
		"compatibility"		=> "18*"
	);
}

function zdexcludegroupfromstat_activate()
{
  if(!file_exists(PLUGINLIBRARY)) return;
	global $PL;
	
	$PL or require_once PLUGINLIBRARY;
	
	$debug = array();
/*
		$query = $db->simple_select("users", "uid, username", "", array('order_by' => 'regdate', 'order_dir' => 'DESC', 'limit' => 1));
		*/
	$search1 =  'if(array_key_exists(\'numusers\', $changes))';
	$replace1 = 'if(true || array_key_exists(\'numusers\', $changes))';

	$search2  =  '$query = $db->simple_select("users", "uid, username", "", array(\'order_by\' => \'regdate\', \'order_dir\' => \'DESC\', \'limit\' => 1));';
	$replace2 =  '$query = $db->simple_select("users u", "u.uid, u.username, COUNT(u.uid) as numusers", "u.usergroup IN (SELECT g.gid FROM mybb_usergroups g WHERE g.showmemberlist=1 AND u.usergroup=g.gid)", array("order_by" => "u.regdate", "order_dir" => "DESC", "limit" => 1));';

	$search3 =	'$new_stats[\'lastuid\'] = $lastmember[\'uid\'];';
	$before3 = '$new_stats[\'numusers\'] = $lastmember[\'numusers\'];';

  $edits = array(
		array(
		'search' => $search1,
		'replace' => $replace1
		),
		array(
			'search' => $search2,
			'replace' => $replace2
		),
		array(
			'search' => $search3,
			'before' => $before3
		)
	);
  
  $result = $PL->edit_core('zdexcludegroupfromstat1', './inc/functions.php', $edits, true);
}

function zdexcludegroupfromstat_deactivate(){
  if(!file_exists(PLUGINLIBRARY)) return;

  global $PL;
  $PL or require_once PLUGINLIBRARY;
	$search  = 'if(array_key_exists(\'numusers\', $changes))';
	$search2 = '$query = $db->simple_select("users", "uid, username", "", array(\'order_by\' => \'regdate\', \'order_dir\' => \'DESC\', \'limit\' => 1));';
	$search3 = '$new_stats[\'lastuid\'] = $lastmember[\'uid\'];';

  $edits = array(
		array(
			'search' => $search,
		),
		array(
			'search' => $search2,
		),
		array(
			'search' => $search3
		)
	);
	$result = $PL->edit_core('zdexcludegroupfromstat1', './inc/functions.php', $edits, true);
}

function processNumUser(){
  global $where;
  $where = "usergroup != 12";
}

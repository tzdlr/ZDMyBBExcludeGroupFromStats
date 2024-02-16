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

$plugins->add_hook("stats_rebuild_numuser", "processNumUser");
// $plugins->add_hook("stats_rebuild_processed", "processStat");

// basic info for plugin manager
function zdexcludegroupfromstat_info()
{
	global $lang;
	$lang->load("zdexcludegroupfromstat", true);

	return array(
		"name"				=> $lang->plugin_name,
		"description"		=> $lang->plugin_description,
		"website"			=> "http://www.znapdev.de",
		"author"			=> "ZnapShot",
		"authorsite"		=> "http://www.znapdev.de",
		"version"			=> "1.0",
		"codename"			=> "zdexcludegroupfromstat",
		"compatibility"		=> "18*"
	);
}

function zdexcludegroupfromstat_activate()
{
  if(!file_exists(PLUGINLIBRARY)) return;
	global $db, $cache;
	global $PL;
	$PL or require_once PLUGINLIBRARY;
	
	$debug = array();
	
	$edits = array(
		'search' => '$query = $db->simple_select("users", "COUNT(uid) AS users");',
		'replace' => '$query = $db->simple_select("users u", "COUNT(u.uid) AS users","u.usergroup IN (SELECT g.gid FROM mybb_usergroups g WHERE g.showmemberlist=1 AND u.usergroup=g.gid)");',
	);

  $result = $PL->edit_core('zdexcludegroupfromstat', './inc/functions_rebuild.php', $edits, true);

  $edits = array(
		'search' => '$query = $db->simple_select("users", "uid, username", "", array(\'order_by\' => \'regdate\', \'order_dir\' => \'DESC\', \'limit\' => 1));',
		'replace' => '$query = $db->simple_select("users u", "u.uid, u.username", "u.usergroup IN (SELECT g.gid FROM mybb_usergroups g WHERE g.showmemberlist=1 AND u.usergroup=g.gid)", array(\'order_by\' => \'regdate\', \'order_dir\' => \'DESC\', \'limit\' => 1));',
	);
  $result = $PL->edit_core('zdexcludegroupfromstat', './inc/functions.php', $edits, true);
}

function zdexcludegroupfromstat_deactivate(){
  if(!file_exists(PLUGINLIBRARY)) return;

  global $PL;
  $PL or require_once PLUGINLIBRARY;

  $result = $PL->edit_core('zdexcludegroupfromstat', 'search.php');

}

function processNumUser(){
  global $where;
  $where = "usergroup != 12";
}

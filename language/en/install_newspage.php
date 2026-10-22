<?php
/**
 *
 * NV Newspage extension for the phpBB Forum Software package
 *
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'NEWSPAGE_NOT_ENABLEABLE'	=> 'NV Newspage could not be enabled. It needs phpBB 3.3.19 or later, PHP 8.3.36 or later, and the PHP DOM extension.',
	'NEWSPAGE_DISABLE_OLD'		=> 'Disable the old “%s” extension first. Keep its data; do not delete it.',
]);

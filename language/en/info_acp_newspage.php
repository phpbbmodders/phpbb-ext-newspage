<?php
/**
 *
 * NV Newspage extension for the phpBB Forum Software package
 *
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_NEWSPAGE' => 'Newspage',
	'ACP_NEWSPAGE_SETTINGS' => 'Settings',
	'ACP_NEWSPAGE_SETTINGS_EXPLAIN' => 'Configure the news forums and how news is displayed.',
	'LOG_NEWSPAGE_SETTINGS' => '<strong>Changed Newspage settings</strong>',
	'NEWS_ICON' => 'News navigation icon',
	'NEWS_ICON_EXPLAIN' => 'Font Awesome icon class, for example fa-bullhorn or fa-rss. phpBB 3.3 uses Font Awesome 4.7 and phpBB 4.0 uses Font Awesome 6, so choose a name both have.',
	'NEWS_ICON_INVALID' => 'Enter a Font Awesome icon class in the format fa-bullhorn.',
));

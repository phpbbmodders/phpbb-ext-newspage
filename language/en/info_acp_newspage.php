<?php

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
	'NEWS_ICON_EXPLAIN' => 'Font Awesome 4.7 icon class, for example fa-bullhorn, fa-newspaper-o, or fa-rss.',
	'NEWS_ICON_INVALID' => 'Enter a Font Awesome icon class in the format fa-bullhorn.',
));

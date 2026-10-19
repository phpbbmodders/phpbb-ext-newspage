<?php

// Run against a disposable board after enabling the extension.
if ($argc !== 3 || !in_array($argv[2], array('enabled', 'preserved', 'purged'), true))
{
	fwrite(STDERR, "Usage: php tests/runtime.php /path/to/phpBB enabled|preserved|purged\n");
	exit($argc === 1 ? 0 : 1);
}
define('IN_PHPBB', true);
$phpbb_root_path = rtrim($argv[1], '/\\') . '/';
$phpEx = 'php';
require $phpbb_root_path . 'common.php';
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewtopic');

if ($argv[2] === 'purged')
{
	if (isset($config['news_number']))
	{
		throw new \RuntimeException('Purge left the news configuration behind.');
	}
	echo "Extension purge removed its configuration.\n";
	exit;
}
if (!$phpbb_container->get('ext.manager')->is_enabled('nickvergessen/newspage'))
{
	throw new \RuntimeException('Extension was not enabled.');
}
$phpbb_container->get('nickvergessen.newspage.controller');
$phpbb_container->get('nickvergessen.newspage.controller.settings');

$text = '[b]Formatted XML news[/b] ' . str_repeat('More news. ', 30);
$uid = $bitfield = '';
$flags = 0;
generate_text_for_storage($text, $uid, $bitfield, $flags, true, true, true);
$html = generate_text_for_display($text, $uid, $bitfield, $flags);
$trimmed = (new \nickvergessen\newspage\message_trimmer())->trim($html, 40);
if (strpos($text, '<r>') !== 0 || strpos($trimmed, '<strong') === false || strpos($trimmed, '...') === false)
{
	throw new \RuntimeException('Formatted XML news could not be rendered and shortened.');
}
if ($argv[2] === 'preserved' && (int) $config['news_number'] !== 3)
{
	throw new \RuntimeException('Re-enabling changed saved news settings.');
}
$config->set('news_number', 3);
echo "Service loading, XML rendering, trimming, and configuration checks passed.\n";

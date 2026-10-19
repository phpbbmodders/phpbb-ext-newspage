<?php

// Standalone regression checks using the target board's UTF-8 helpers.
if ($argc !== 2)
{
	fwrite(STDERR, "Usage: php tests/compatibility.php /path/to/phpBB\n");
	exit($argc === 1 ? 0 : 1);
}

$root = rtrim($argv[1], '/\\') . '/';
define('IN_PHPBB', true);
require $root . 'vendor/autoload.php';
require $root . 'includes/utf/utf_tools.php';
require __DIR__ . '/../message_trimmer.php';

$trimmer = new \nickvergessen\newspage\message_trimmer();
$cases = array(
	array('<strong>abc</strong>', 0, '<strong>abc</strong>'),
	array('<strong>abc</strong>', 3, '<strong>abc</strong>'),
	array('<strong>abcdef</strong><em>tail</em>', 3, '<strong>abc</strong>...'),
	array('<p>A &amp; B</p><p>tail</p>', 3, '<p>A &amp;</p>...'),
	array('<a href="https://example.com">abcdef</a>', 3, '<a href="https://example.com">abc</a>...'),
	array('<p>' . "\xc3\xa9\xf0\x9f\x98\x80" . 'tail</p>', 2, '<p>' . "\xc3\xa9\xf0\x9f\x98\x80" . '</p>...'),
	array('<strong>a<em>bcdef</em></strong>', 3, '<strong>a<em>bc</em></strong>...'),
	array('<p>abc</p><img src="example.png" alt=""><p>def</p>', 3, '<p>abc</p>...'),
);

foreach ($cases as $index => $case)
{
	$result = $trimmer->trim($case[0], $case[1]);
	if ($result !== $case[2])
	{
		fwrite(STDERR, 'Trimming case ' . $index . ' failed: ' . $result . "\n");
		exit(1);
	}
}
echo count($cases) . " message trimming regression checks passed.\n";

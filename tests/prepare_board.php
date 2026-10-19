<?php

// Writes installer configuration only for a new disposable test board.
if ($argc !== 2)
{
	fwrite(STDERR, "Usage: php tests/prepare_board.php /path/to/phpBB\n");
	exit($argc === 1 ? 0 : 1);
}
$root = realpath($argv[1]);
if (!$root || !is_file($root . '/install/phpbbcli.php') || is_file($root . '/config.php') && filesize($root . '/config.php') > 0 || is_file($root . '/store/newspage.sqlite3'))
{
	throw new \RuntimeException('A fresh, uninstalled phpBB source directory is required.');
}
require $root . '/vendor/autoload.php';
$settings = array('installer' => array(
	'admin' => array('name' => 'admin', 'password' => 'test-admin-password', 'email' => 'admin@example.com'),
	'board' => array('lang' => 'en', 'name' => 'Newspage CI', 'description' => 'Disposable test board'),
	'database' => array('dbms' => 'sqlite3', 'dbhost' => $root . '/store/newspage.sqlite3', 'dbport' => null, 'dbuser' => null, 'dbpasswd' => null, 'dbname' => null, 'table_prefix' => 'phpbb_'),
	'email' => array('enabled' => false),
	'server' => array('cookie_secure' => false, 'server_protocol' => 'http://', 'force_server_vars' => false, 'server_name' => 'localhost', 'server_port' => 8080, 'script_path' => '/'),
	'extensions' => array(),
));
$path = $root . '/store/newspage-install.yml';
if (file_put_contents($path, \Symfony\Component\Yaml\Yaml::dump($settings, 4)) === false)
{
	throw new \RuntimeException('Could not write the disposable installer configuration.');
}
echo $path . "\n";

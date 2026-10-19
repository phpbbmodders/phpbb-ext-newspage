<?php

define('IN_PHPBB', true);
define('DEBUG_TEST', true);
$phpbb_root_path = rtrim(getenv('PHPBB_ROOT') ?: __DIR__ . '/../../../..', '/\\') . '/';
$phpEx = 'php';
require $phpbb_root_path . 'includes/startup.php';
require $phpbb_root_path . 'includes/constants.php';
require $phpbb_root_path . 'phpbb/class_loader.php';
require $phpbb_root_path . 'includes/utf/utf_tools.php';
require $phpbb_root_path . 'includes/functions.php';

$core_loader = new \phpbb\class_loader('phpbb\\', $phpbb_root_path . 'phpbb/');
$core_loader->register();
$extension_loader = new \phpbb\class_loader('nickvergessen\\newspage\\', __DIR__ . '/../');
$extension_loader->register();

$config = new \phpbb\config\config(array('form_token_lifetime' => -1));
$user = new \phpbb\user(new \phpbb\language\language(new \phpbb\language\language_file_loader($phpbb_root_path, 'php')), '\\phpbb\\datetime');
$user->data = array('user_id' => 2, 'user_form_salt' => '');

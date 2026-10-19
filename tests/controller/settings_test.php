<?php

/**
 *
 * NV Newspage extension for the phpBB Forum Software package
 *
 * @copyright (c) nickvergessen <https://github.com/nickvergessen>
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\newspage\tests\controller;

use phpbbmodders\newspage\controller\settings;
use phpbb\config\config;
use phpbb\exception\http_exception;

/**
 * Testing \phpbbmodders\newspage\controller\settings
 */
class settings_test extends \PHPUnit\Framework\TestCase
{
	/**
	 * Submitted form values and the settings they should save.
	 *
	 * @return array
	 */
	public function save_settings_data()
	{
		return array(
			'values are saved' => array(
				array(
					'news_char_limit'	=> 250,
					'news_forums'		=> array(2, 5),
					'news_number'		=> 7,
					'news_pages'		=> 3,
					'news_user_info'	=> true,
					'news_post_buttons'	=> false,
					'news_shadow_show'	=> true,
					'news_attach_show'	=> false,
					'news_cat_show'		=> true,
					'news_archive_show'	=> true,
				),
				array(
					'news_char_limit'	=> 250,
					'news_forums'		=> '2,5',
					'news_number'		=> 7,
					'news_pages'		=> 3,
					'news_user_info'	=> true,
					'news_post_buttons'	=> false,
					'news_shadow'		=> true,
					'news_attach_show'	=> false,
					'news_cat_show'		=> true,
					'news_archive_show'	=> true,
				),
			),
			'limits are applied' => array(
				array(
					'news_char_limit'	=> 20,
					'news_number'		=> 0,
					'news_pages'		=> -2,
				),
				array(
					'news_char_limit'	=> 100,
					'news_forums'		=> '0',
					'news_number'		=> 1,
					'news_pages'		=> 1,
				),
			),
			'0 characters means no shortening' => array(
				array(
					'news_char_limit'	=> 0,
				),
				array(
					'news_char_limit'	=> 0,
				),
			),
		);
	}

	/**
	 * @dataProvider save_settings_data
	 *
	 * @param array $submitted Form values by name
	 * @param array $expected  Config values that must be saved
	 */
	public function test_save_settings(array $submitted, array $expected)
	{
		$config = new config(array());
		$this->get_controller(true, $submitted, $config)->save_settings();

		foreach ($expected as $name => $value)
		{
			$this->assertEquals($value, $config[$name], $name);
		}
	}

	public function test_save_settings_needs_a_board()
	{
		$config = new config(array());

		try
		{
			$this->get_controller(false, array('news_number' => 9), $config)->save_settings();
			$this->fail('Expected \phpbb\exception\http_exception to be thrown');
		}
		catch (http_exception $exception)
		{
			$this->assertEquals(403, $exception->getStatusCode());
			$this->assertEquals('NO_AUTH_OPERATION', $exception->getMessage());
		}

		$this->assertFalse(isset($config['news_number']));
	}

	/**
	 * @param bool   $is_admin  Whether the user has a_board
	 * @param array  $submitted Form values by name; missing ones get the default
	 * @param config $config
	 * @return settings
	 */
	protected function get_controller($is_admin, array $submitted, config $config)
	{
		$auth = $this->createMock('\phpbb\auth\auth');
		$auth->method('acl_get')
			->willReturnCallback(function ($option) use ($is_admin) {
				return $option === 'a_board' && $is_admin;
			});

		$request = $this->createMock('\phpbb\request\request');
		$request->method('variable')
			->willReturnCallback(function ($name, $default) use ($submitted) {
				return array_key_exists($name, $submitted) ? $submitted[$name] : $default;
			});

		/** @var \phpbb\auth\auth $auth */
		/** @var \phpbb\request\request $request */
		return new settings($auth, $config, $request);
	}
}

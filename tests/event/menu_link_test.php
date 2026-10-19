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

namespace phpbbmodders\newspage\tests\event;

use phpbbmodders\newspage\event\menu_link_listener;
use phpbbmodders\newspage\helper;
use phpbb\config\config;
use phpbb\event\data;

/**
 * Class menu_link_test
 * Testing \phpbbmodders\newspage\event\menu_link_listener
 *
 * @package phpbbmodders\newspage\tests\event
 */
class menu_link_test extends \PHPUnit\Framework\TestCase
{
	/** @var \PHPUnit_Framework_MockObject_MockObject */
	protected $template;

	/** @var menu_link_listener */
	protected $listener;

	/**
	 * @return null
	 */
	public function setup_listener($icon = 'fa-bullhorn')
	{
		$this->template = $template = $this->getMockBuilder('\phpbb\template\template')
			->getMock();

		$controller_helper = $this->getMockBuilder('\phpbb\controller\helper')
			->disableOriginalConstructor()
			->getMock();
		$controller_helper->expects($this->any())
			->method('route')
			->willReturnCallback(function ($route, array $params = array()) {
				return $route . '#' . serialize($params);
			});

		/** @var \phpbb\controller\helper $controller_helper */
		/** @var \phpbb\template\template $template */
		$this->listener = new menu_link_listener(
			new helper(
				$controller_helper,
				new config(array())
			),
			$template,
			new config(array('news_icon' => $icon))
		);
	}

	/**
	 * @return null
	 */
	public function test_construct()
	{
		$this->setup_listener();
		$this->assertInstanceOf('\Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->listener);
	}

	/**
	 * @return null
	 */
	public function test_getSubscribedEvents()
	{
		$this->assertEquals(array(
			'core.user_setup',
			'core.page_header',
		), array_keys(menu_link_listener::getSubscribedEvents()));
	}

	/**
	 * @return null
	 */
	public function load_language_on_setup_data()
	{
		return array(
			array(
				array(),
				array(
					array(
						'ext_name' => 'phpbbmodders/newspage',
						'lang_set' => 'newspage',
					),
				),
			),
			array(
				array(
					array(
						'ext_name' => 'board3/portal',
						'lang_set' => 'main',
					),
				),
				array(
					array(
						'ext_name' => 'board3/portal',
						'lang_set' => 'main',
					),
					array(
						'ext_name' => 'phpbbmodders/newspage',
						'lang_set' => 'newspage',
					),
				),
			),
		);
	}

	/**
	 * @dataProvider load_language_on_setup_data
	 */
	public function test_load_language_on_setup($lang_set_ext, $expected_contains)
	{
		$this->setup_listener();

		$event_data = array('lang_set_ext');
		$event = new data(compact($event_data));
		$this->listener->load_language_on_setup($event);

		extract($event->get_data_filtered($event_data));

		foreach ($expected_contains as $expected)
		{
			$this->assertContains($expected, $lang_set_ext);
		}
	}

	/**
	 * @return null
	 */
	public function icon_data()
	{
		return array(array('fa-bullhorn'), array('fa-newspaper-o'));
	}

	/** @dataProvider icon_data */
	public function test_add_page_header_link($icon)
	{
		$this->setup_listener($icon);

		$this->template->expects($this->once())
			->method('assign_vars')
			->with(array(
				'U_NEWSPAGE' => 'phpbbmodders_newspage_controller#a:0:{}',
				'NEWSPAGE_ICON' => $icon,
			));

		$this->listener->add_page_header_link();
	}
}

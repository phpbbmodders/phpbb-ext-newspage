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

namespace phpbbmodders\newspage\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class menu_link_listener
 * Creates the link in the head navbar
 *
 * @package phpbbmodders\newspage\event
 */
class menu_link_listener implements EventSubscriberInterface
{
	/* @var \phpbbmodders\newspage\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\config\config */
	protected $config;

	/**
	 * Register to the events
	 *
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return array(
			'core.user_setup'						=> 'load_language_on_setup',
			'core.page_header'						=> 'add_page_header_link',
		);
	}

	/**
	 * Constructor
	 *
	 * @param \phpbbmodders\newspage\helper $helper
	 * @param \phpbb\template\template $template
	 * @param \phpbb\config\config $config
	 */
	public function __construct(\phpbbmodders\newspage\helper $helper, \phpbb\template\template $template, \phpbb\config\config $config)
	{
		$this->helper = $helper;
		$this->template = $template;
		$this->config = $config;
	}

	/**
	 * @param array $event
	 * @return null
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'phpbbmodders/newspage',
			'lang_set' => 'newspage',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * @return null
	 */
	public function add_page_header_link()
	{
		$this->template->assign_vars(array(
			'U_NEWSPAGE'	=> $this->helper->generate_route()->get_url(),
			'NEWSPAGE_ICON' => isset($this->config['news_icon']) ? $this->config['news_icon'] : 'fa-bullhorn',
		));
	}
}

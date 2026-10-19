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
 * Class viewonline_listener
 * Handles displaying the route name and link on viewonline.php
 *
 * @package phpbbmodders\newspage\event
 */
class viewonline_listener implements EventSubscriberInterface
{
	/**
	 * Register to the events
	 *
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return array(
			'core.viewonline_overwrite_location'	=> 'add_newspage_viewonline',
		);
	}

	/* @var \phpbbmodders\newspage\helper */
	protected $helper;

	/* @var \phpbb\user */
	protected $user;

	/* @var string phpEx */
	protected $php_ext;

	/**
	 * Constructor
	 *
	 * @param \phpbbmodders\newspage\helper $helper
	 * @param \phpbb\user $user
	 * @param string $php_ext
	 */
	public function __construct(\phpbbmodders\newspage\helper $helper, \phpbb\user $user, $php_ext)
	{
		$this->helper = $helper;
		$this->user = $user;
		$this->php_ext = $php_ext;
	}

	/**
	 * Name the news page a user is on in the Who is online list.
	 *
	 * @param \phpbb\event\data $event
	 * @return void
	 */
	public function add_newspage_viewonline($event)
	{
		$parts = $this->get_news_route_parts(isset($event['row']['session_page']) ? $event['row']['session_page'] : '');
		if ($parts === false)
		{
			return;
		}

		$route = $this->helper->generate_route();
		$forum_id = $this->get_category_from_route($parts);
		$archive = $this->get_archive_from_route($parts);

		if (count($parts) === 1)
		{
			$event['location_url'] = $route->get_url();
			$event['location'] = $this->user->lang('VIEWONLINE_NEWS');
		}
		else if ($forum_id && $archive)
		{
			$archive_start = $this->user->get_timestamp_from_format('Y/n-d H:i:s', $archive . '-01 0:00:00');

			$event['location_url'] = $route->get_url($forum_id, $archive);
			$event['location'] = $this->user->lang('VIEWONLINE_NEWS_CATEGORY_ARCHIVE', $event['forum_data'][$forum_id]['forum_name'], $this->user->format_date($archive_start, 'F Y'));
		}
		else if ($forum_id)
		{
			$event['location_url'] = $route->get_url($forum_id);
			$event['location'] = $this->user->lang('VIEWONLINE_NEWS_CATEGORY', $event['forum_data'][$forum_id]['forum_name']);
		}
		else if ($archive)
		{
			$archive_start = $this->user->get_timestamp_from_format('Y/n-d H:i:s', $archive . '-01 0:00:00');

			$event['location_url'] = $route->get_url(false, $archive);
			$event['location'] = $this->user->lang('VIEWONLINE_NEWS_ARCHIVE', $this->user->format_date($archive_start, 'F Y'));
		}
	}

	/**
	 * Split a session page into its route parts when it is a news page.
	 *
	 * phpBB 3.3 records news pages as "app.php/news/...", phpBB 4.0 as
	 * "index.php/news/...". Both become ['news', ...].
	 *
	 * @param string $session_page The session's page, as phpBB stores it
	 * @return array|bool The parts from "news" on, or false for other pages
	 */
	protected function get_news_route_parts($session_page)
	{
		$path = (string) parse_url($session_page, PHP_URL_PATH);
		$parts = explode('/', trim($path, '/'));

		if (in_array($parts[0], array('app.' . $this->php_ext, 'index.' . $this->php_ext), true))
		{
			array_shift($parts);
		}

		return (isset($parts[0]) && $parts[0] === 'news') ? $parts : false;
	}

	/**
	 * @param array $parts Route parts from get_news_route_parts()
	 * @return int|bool
	 */
	protected function get_category_from_route(array $parts)
	{
		if (isset($parts[1], $parts[2]) && $parts[1] === 'category')
		{
			return (int) $parts[2];
		}

		return false;
	}

	/**
	 * @param array $parts Route parts from get_news_route_parts()
	 * @return string|bool Year and month as "Y/n"
	 */
	protected function get_archive_from_route(array $parts)
	{
		if (isset($parts[1], $parts[3]) && $parts[1] === 'archive')
		{
			return (int) $parts[2] . '/' . (int) $parts[3];
		}
		else if (isset($parts[3], $parts[5]) && $parts[3] === 'archive')
		{
			return (int) $parts[4] . '/' . (int) $parts[5];
		}

		return false;
	}
}

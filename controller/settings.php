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

namespace phpbbmodders\newspage\controller;

use phpbb\exception\http_exception;

/**
 * Saves the news page settings from the ACP settings form.
 */
class settings
{
	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\request\request_interface */
	protected $request;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth $auth
	 * @param \phpbb\config\config $config
	 * @param \phpbb\request\request_interface $request
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\request\request_interface $request)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->request = $request;
	}

	/**
	 * Save the submitted settings. The caller checks the form token.
	 *
	 * @return void
	 * @throws http_exception When the user may not change board settings
	 */
	public function save_settings()
	{
		if (!$this->auth->acl_get('a_board'))
		{
			throw new http_exception(403, 'NO_AUTH_OPERATION');
		}

		if ($this->request->variable('news_char_limit', 0) !== 0)
		{
			$this->config->set('news_char_limit',	max(100, $this->request->variable('news_char_limit', 0)));
		}
		else
		{
			// "0" means no trimming
			$this->config->set('news_char_limit', 0);
		}

		$this->config->set('news_forums',		implode(',', $this->request->variable('news_forums', array(0))));
		$this->config->set('news_number',		max(1, $this->request->variable('news_number', 0)));
		$this->config->set('news_pages',		max(1, $this->request->variable('news_pages', 0)));
		$this->config->set('news_user_info',	$this->request->variable('news_user_info', false));
		$this->config->set('news_post_buttons',	$this->request->variable('news_post_buttons', false));
		$this->config->set('news_shadow',		$this->request->variable('news_shadow_show', false));
		$this->config->set('news_attach_show',	$this->request->variable('news_attach_show', false));
		$this->config->set('news_cat_show',		$this->request->variable('news_cat_show', false));
		$this->config->set('news_archive_show',	$this->request->variable('news_archive_show', false));
		$this->config->set('news_lead_image',	$this->request->variable('news_lead_image', false));
	}
}

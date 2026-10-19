<?php
/**
 *
 * NV Newspage extension for the phpBB Forum Software package
 *
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\newspage\acp;

class main_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
		global $auth, $config, $request, $template, $user, $phpbb_container, $phpbb_log;

		$user->add_lang_ext('phpbbmodders/newspage', array('newspage', 'info_acp_newspage'));
		$this->tpl_name = 'acp_newspage_settings';
		$this->page_title = 'ACP_NEWSPAGE_SETTINGS';

		if (!$auth->acl_get('a_board'))
		{
			trigger_error($user->lang('NO_AUTH_OPERATION'), E_USER_WARNING);
		}

		add_form_key('acp_newspage');

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('acp_newspage'))
			{
				trigger_error($user->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$icon = trim($request->variable('news_icon', 'fa-bullhorn'));
			if (!preg_match('/\Afa-[a-z0-9]+(?:-[a-z0-9]+)*\z/', $icon))
			{
				trigger_error($user->lang('NEWS_ICON_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$phpbb_container->get('phpbbmodders.newspage.controller.settings')->save_settings();
			$config->set('news_icon', $icon);
			$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_NEWSPAGE_SETTINGS');
			trigger_error($user->lang('NEWS_SAVED') . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'U_ACTION' => $this->u_action,
			'SETTING_NEWS_ICON' => isset($config['news_icon']) ? $config['news_icon'] : 'fa-bullhorn',
			'SETTING_NEWS_FORUMS' => make_forum_select(explode(',', $config['news_forums']), false, false, true),
			'SETTING_NEWS_NUMBER' => (int) $config['news_number'],
			'SETTING_NEWS_PAGES' => (int) $config['news_pages'],
			'SETTING_NEWS_CHAR_LIMIT' => (int) $config['news_char_limit'],
			'SETTING_NEWS_CAT_SHOW' => (bool) $config['news_cat_show'],
			'SETTING_NEWS_ARCHIVE_SHOW' => (bool) $config['news_archive_show'],
			'SETTING_NEWS_SHADOW_SHOW' => (bool) $config['news_shadow'],
			'SETTING_NEWS_USER_INFO' => (bool) $config['news_user_info'],
			'SETTING_NEWS_POST_BUTTONS' => (bool) $config['news_post_buttons'],
			'SETTING_NEWS_ATTACH_SHOW' => (bool) $config['news_attach_show'],
		));
	}
}

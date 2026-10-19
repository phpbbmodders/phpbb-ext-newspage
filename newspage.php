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

namespace phpbbmodders\newspage;

/**
 * Class newspage
 *
 * @package phpbbmodders\newspage
 */
class newspage
{
	/** @var \phpbb\auth\auth */
	protected $auth;
	/** @var \phpbb\cache\service */
	protected $cache;
	/** @var \phpbb\config\config */
	protected $config;
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\profilefields\manager */
	protected $profile_fields;
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\user */
	protected $user;
	/** @var \phpbb\content_visibility */
	protected $content_visibility;
	/** @var \phpbb\controller\helper */
	protected $helper;
	/** @var \phpbbmodders\newspage\helper */
	protected $news_helper;
	/** @var \phpbb\pagination */
	protected $pagination;
	/** @var \phpbb\avatar\helper|null phpBB 4.0's avatar helper; null on phpBB 3.3 */
	protected $avatar_helper;

	/** @var string */
	protected $root_path;
	/** @var string */
	protected $php_ext;

	/** @var int */
	protected $num_pagination_items = 0;
	/** @var string */
	protected $page_title;
	/** @var int */
	protected $start = 0;
	/** @var int */
	protected $news = 0;
	/** @var array */
	protected $archive = array('y' => 0, 'm' => 0);
	/** @var int */
	protected $category = 0;

	/** @var int[] */
	protected $post_ids;
	/** @var int[] */
	protected $topic_ids;
	/** @var int[] */
	protected $forums;
	/** @var int[] */
	protected $poster_ids;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth		$auth		Auth object
	* @param \phpbb\cache\service	$cache		Cache object
	* @param \phpbb\config\config	$config		Config object
	* @param \phpbb\db\driver\driver_interface	$db		Database object
	* @param \phpbb\profilefields\manager	$profile_fields	Profile fields manager
	* @param \phpbb\template\template	$template	Template object
	* @param \phpbb\user		$user		User object
	* @param \phpbb\content_visibility		$content_visibility	Content visibility object
	* @param \phpbb\controller\helper		$helper				Controller helper object
	* @param \phpbbmodders\newspage\helper $news_helper				Controller helper object
	* @param \phpbb\pagination	$pagination	Pagination object
	* @param string			$root_path	phpBB root path
	* @param string			$php_ext	phpEx
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\cache\service $cache, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\profilefields\manager $profile_fields, \phpbb\template\template $template, \phpbb\user $user, \phpbb\content_visibility $content_visibility, \phpbb\controller\helper $helper, \phpbbmodders\newspage\helper $news_helper, \phpbb\pagination $pagination, $root_path, $php_ext)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->profile_fields = $profile_fields;
		$this->template = $template;
		$this->user = $user;
		$this->content_visibility = $content_visibility;
		$this->helper = $helper;
		$this->news_helper = $news_helper;
		$this->pagination = $pagination;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		if (!function_exists('phpbb_get_user_rank'))
		{
			include($this->root_path . 'includes/functions_display.' . $this->php_ext);
		}
		$this->page_title = $this->user->lang['NEWS'];
	}

	/**
	 * Set phpBB 4.0's avatar helper. phpBB 3.3 has none and passes null.
	 *
	 * @param \phpbb\avatar\helper|null $avatar_helper
	 * @return void
	 */
	public function set_avatar_helper($avatar_helper = null)
	{
		$this->avatar_helper = $avatar_helper;
	}

	/**
	 * The poster's avatar as an image tag, as phpBB 3.3 makes it.
	 *
	 * phpBB 4.0 has no phpbb_get_user_avatar(); its avatar helper returns the
	 * avatar's data instead, which is turned into the same markup here so the
	 * news templates work on both versions.
	 *
	 * @param array $row User row with the user_avatar* columns
	 * @return string Image tag, or an empty string for no avatar
	 */
	protected function get_poster_avatar(array $row)
	{
		if (function_exists('phpbb_get_user_avatar'))
		{
			return phpbb_get_user_avatar($row);
		}

		if ($this->avatar_helper === null)
		{
			return '';
		}

		$avatar = $this->avatar_helper->get_user_avatar($row);
		if (empty($avatar['src']))
		{
			return '';
		}

		return '<img class="avatar" src="' . utf8_htmlspecialchars($avatar['src']) . '"'
			. ' width="' . (int) $avatar['width'] . '" height="' . (int) $avatar['height'] . '"'
			. ' alt="' . utf8_htmlspecialchars($avatar['title']) . '" />';
	}

	/**
	 * Take a post's first image out of its text, or else out of its attachment list.
	 *
	 * @param string   $message     Rendered post text, attachments filled in
	 * @param string[] $attachments Rendered attachments not shown in the text
	 * @return array 'html' and 'attachments' without the image; 'src' and 'alt' of the
	 *               image, both '' when the post has none
	 */
	protected function take_lead_image($message, array $attachments)
	{
		$lead_image = new lead_image();
		$taken = $lead_image->take($message);
		$taken['attachments'] = $attachments;

		if ($taken['src'] === '')
		{
			foreach ($attachments as $key => $attachment)
			{
				$from_attachment = $lead_image->take($attachment);
				if ($from_attachment['src'] !== '')
				{
					$taken['src'] = $from_attachment['src'];
					$taken['alt'] = $from_attachment['alt'];
					unset($taken['attachments'][$key]);
					break;
				}
			}
		}

		return $taken;
	}

	/**
	 * @return string
	 */
	public function get_page_title()
	{
		return $this->page_title;
	}

	/**
	 * @param int $start
	 * @return $this
	 */
	public function set_start($start)
	{
		$this->start = (int) $start;

		return $this;
	}

	/**
	 * @param int $category
	 * @return $this
	 */
	public function set_category($category)
	{
		$this->category = (int) $category;

		return $this;
	}

	/**
	 * @param int $news
	 * @return $this
	 */
	public function set_news($news)
	{
		$this->news = (int) $news;

		return $this;
	}

	/**
	 * @param int $year
	 * @param int $month
	 * @return $this
	 */
	public function set_archive($year, $month)
	{
		$this->archive = array(
			'y'	=> (int) $year,
			'm'	=> (int) $month,
		);

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isset_archive()
	{
		return isset($this->archive['y']) && isset($this->archive['m']);
	}

	/**
	 * @return bool
	 */
	public function is_archive()
	{
		return $this->isset_archive() && $this->archive['y'] !== 0 && $this->archive['m'] !== 0;
	}

	/**
	 * Is the archive the current archive?
	 *
	 * @param int $year
	 * @param int $month
	 * @return bool
	 */
	protected function is_current_archive($year, $month)
	{
		return $this->is_archive() && $this->archive['y'] == $year && $this->archive['m'] == $month;
	}

	/**
	 * Is the archive important for the pagination?
	 *
	 * @param int $year
	 * @param int $month
	 * @return bool
	 */
	protected function is_paginated_archive($year, $month)
	{
		return !$this->isset_archive() ||
		$this->is_current_archive($year, $month) ||
		($this->archive['y'] == 0 && $this->archive['m'] == 0);
	}

	/**
	 * Get the news items we want to display
	 */
	public function get_news_ids()
	{
		/**
		* Select topic_ids for the news
		*/
		$sql = $this->db->sql_build_query('SELECT', $this->get_newstopic_sql());
		if ($this->news)
		{
			$result = $this->db->sql_query($sql);
		}
		else
		{
			$result = $this->db->sql_query_limit($sql, $this->config['news_number'], $this->start);
		}

		$this->forums = $this->topic_ids = $this->post_ids = $this->poster_ids = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->post_ids[] = (int) $row['topic_first_post_id'];
			$this->topic_ids[] = (int) $row['topic_id'];
			$this->poster_ids[] = (int) $row['topic_poster'];
			$this->forums[(int) $row['forum_id']][] = (int) $row['topic_id'];
		}
		$this->db->sql_freeresult($result);
	}

	public function base()
	{
		$this->get_news_ids();

		if (empty($this->topic_ids))
		{
			$l_no_news = ($this->is_archive()) ? 'NO_NEWS_ARCHIVE' : (($this->category) ? 'NO_NEWS_CATEGORY' : 'NO_NEWS');
			$this->template->assign_var('L_NO_NEWS', $this->user->lang($l_no_news));

			return;
		}

		// The "last edited by" notice uses viewtopic's language strings
		$this->user->add_lang('viewtopic');

		// Grab icons, attachments and profile fields
		$icons = $this->cache->obtain_icons();
		$profile_fields = ($this->config['news_user_info']) ? $this->get_profile_fields($this->poster_ids) : array();
		$attachments = (!empty($this->post_ids)) ? $this->get_attachments($this->post_ids) : array();

		// Get topic tracking
		foreach ($this->forums as $forum_id => $topic_ids)
		{
			$topic_tracking_info[$forum_id] = get_complete_topic_tracking($forum_id, $topic_ids);
		}

		$sql_array = array(
			'SELECT'	=> 't.*, p.*, u.*, f.forum_name',
			'FROM'		=> array(TOPICS_TABLE => 't'),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(FORUMS_TABLE => 'f'),
					'ON'	=> 'f.forum_id = t.forum_id',
				),
				array(
					'FROM'	=> array(POSTS_TABLE => 'p'),
					'ON'	=> 'p.post_id = t.topic_first_post_id',
				),
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'u.user_id = p.poster_id',
				),
			),
			'ORDER_BY'	=> 't.topic_time ' . (($this->is_archive()) ? 'ASC' : 'DESC'),
			'WHERE'		=> $this->db->sql_in_set('t.topic_id', $this->topic_ids),
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$editors = $this->get_editors($rows);

		foreach ($rows as $row)
		{
			// Set some variables
			$post_id = (int) $row['post_id'];
			$poster_id = (int) $row['poster_id'];
			$topic_id = (int) $row['topic_id'];
			$forum_id = (int) $row['forum_id'];
			$display_notice = false;

			$post_unread = (isset($topic_tracking_info[$forum_id][$topic_id]) && $row['post_time'] > $topic_tracking_info[$forum_id][$topic_id]) ? true : false;

			//parse message for display
			if ($this->news)
			{
				$this->page_title = censor_text($row['post_subject']);
			}

			$parse_flags = ($row['enable_bbcode'] ? OPTION_FLAG_BBCODE : 0) | ($row['enable_smilies'] ? OPTION_FLAG_SMILIES : 0) | ($row['enable_magic_url'] ? OPTION_FLAG_LINKS : 0);
			$message = generate_text_for_display($row['post_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $parse_flags, true);
			// The first image is shown above the text on the news list, if the board wants it.
			// Attachments are filled in first, so an image attachment can be that image.
			$lead_image = array('src' => '', 'alt' => '');
			$attachments_parsed = false;
			if (!$this->news && !empty($this->config['news_lead_image']))
			{
				if ($this->auth->acl_get('f_download', $forum_id) && !empty($attachments[$post_id]))
				{
					parse_attachments($forum_id, $message, $attachments[$post_id], $update_count);
					$attachments_parsed = true;
				}
				$lead_image = $this->take_lead_image($message, $attachments_parsed ? $attachments[$post_id] : array());
				$message = $lead_image['html'];
				if ($attachments_parsed)
				{
					$attachments[$post_id] = $lead_image['attachments'];
				}
			}

			$shortened = false;
			if (!$this->news && (int) $this->config['news_char_limit'] > 0)
			{
				$trimmer = new message_trimmer();
				$full_message = $message;
				$message = $trimmer->trim($message, (int) $this->config['news_char_limit']);
				$shortened = $message !== $full_message;
			}

			if (!$this->auth->acl_get('f_download', $forum_id))
			{
				$display_notice = true;
			}
			else if (!$attachments_parsed && !empty($attachments[$post_id]))
			{
				parse_attachments($forum_id, $message, $attachments[$post_id], $update_count);
			}

			$row['post_text'] = $message;

			// Edit Information
			if ($this->shows_edit_notice($row))
			{
				$editor_id = (int) $row['post_edit_user'];
				if (!$editor_id || $editor_id == $poster_id || !isset($editors[$editor_id]))
				{
					$display_username = get_username_string('full', $poster_id, $row['username'], $row['user_colour'], $row['post_username']);
				}
				else
				{
					$display_username = get_username_string('full', $editor_id, $editors[$editor_id]['username'], $editors[$editor_id]['user_colour']);
				}

				$l_edited_by = $this->user->lang('EDITED_TIMES_TOTAL', (int) $row['post_edit_count'], $display_username, $this->user->format_date($row['post_edit_time'], false, true));
			}
			else
			{
				$l_edited_by = '';
			}

			$flags = ($row['user_sig_bbcode_bitfield'] && $row['enable_bbcode'] ? OPTION_FLAG_BBCODE : 0) | OPTION_FLAG_SMILIES;
			$row['user_sig'] = generate_text_for_display($row['user_sig'], $row['user_sig_bbcode_uid'], $row['user_sig_bbcode_bitfield'], $flags);

			$rank_data = phpbb_get_user_rank($row, $row['user_posts']);
			$row['rank_title'] = $rank_data['title'];
			$row['rank_image'] = $rank_data['img'];
			$row['rank_image_src'] = $rank_data['img_src'];

			$comments = $this->content_visibility->get_count('topic_posts', $row, $forum_id) - 1;

			// Forum names are stored HTML-escaped
			$forum_name = isset($row['forum_name']) ? $row['forum_name'] : '';
			$u_forum = append_sid("{$this->root_path}viewforum.{$this->php_ext}", 'f=' . $forum_id);

			$this->template->assign_block_vars('postrow', array(
				'POST_ID'				=> $post_id,
				'U_EDIT'				=> (!$this->user->data['is_registered']) ? '' : ((($this->user->data['user_id'] == $row['poster_id'] && $this->auth->acl_get('f_edit', $forum_id) && ($row['post_time'] > time() - ($this->config['edit_time'] * 60) || !$this->config['edit_time'])) || $this->auth->acl_get('m_edit', $forum_id)) ? append_sid("{$this->root_path}posting.{$this->php_ext}", "mode=edit&amp;f=$forum_id&amp;p={$row['post_id']}") : ''),
				'U_QUOTE'				=> ($this->auth->acl_get('f_reply', $forum_id)) ? append_sid("{$this->root_path}posting.{$this->php_ext}", "mode=quote&amp;f=$forum_id&amp;p={$row['post_id']}") : '',
				'U_INFO'				=> ($this->auth->acl_get('m_info', $forum_id)) ? append_sid("{$this->root_path}mcp.{$this->php_ext}", "i=main&amp;mode=post_details&amp;f=$forum_id&amp;p=" . $row['post_id'], true, $this->user->session_id) : '',
				'U_DELETE'				=> (!$this->user->data['is_registered']) ? '' : ((($this->user->data['user_id'] == $row['poster_id'] && $this->auth->acl_get('f_delete', $forum_id) && $row['topic_last_post_id'] == $row['post_id'] && ($row['post_time'] > time() - ($this->config['edit_time'] * 60) || !$this->config['edit_time'])) || $this->auth->acl_get('m_delete', $forum_id)) ? append_sid("{$this->root_path}posting.{$this->php_ext}", "mode=delete&amp;f=$forum_id&amp;p={$row['post_id']}") : ''),
				'U_REPORT'				=> ($this->auth->acl_get('f_report', $forum_id)) ? append_sid("{$this->root_path}report.{$this->php_ext}", 'f=' . $forum_id . '&amp;p=' . $row['post_id']) : '',
				'U_WARN'				=> ($this->auth->acl_get('m_warn') && $poster_id != $this->user->data['user_id'] && $poster_id != ANONYMOUS) ? append_sid("{$this->root_path}mcp.{$this->php_ext}", 'i=warn&amp;mode=warn_post&amp;f=' . $forum_id . '&amp;p=' . $post_id, true, $this->user->session_id) : '',
				'U_NEWS'				=> $this->helper->route('phpbbmodders_newspage_singlenews_controller', array('topic_id' => $topic_id)),

				'POST_ICON_IMG'			=> (!empty($row['icon_id'])) ? $icons[$row['icon_id']]['img'] : '',
				'POST_ICON_IMG_WIDTH'	=> (!empty($row['icon_id'])) ? $icons[$row['icon_id']]['width'] : '',
				'POST_ICON_IMG_HEIGHT'	=> (!empty($row['icon_id'])) ? $icons[$row['icon_id']]['height'] : '',
				'U_MINI_POST'			=> append_sid("{$this->root_path}viewtopic.{$this->php_ext}", 'p=' . $row['post_id']) . (($row['topic_type'] == POST_GLOBAL) ? '&amp;f=' . $forum_id : '') . '#p' . $row['post_id'],
				'POST_SUBJECT'			=> htmlspecialchars(censor_text($row['post_subject']), ENT_QUOTES, 'UTF-8', false),
				'S_UNREAD_POST'			=> $post_unread,
				'POST_AUTHOR_FULL'		=> get_username_string('full', $poster_id, $row['username'], $row['user_colour'], $row['post_username']),
				'POST_DATE'				=> $this->user->format_date($row['post_time']),

				'S_POST_REPORTED'		=> ($row['post_reported'] && $this->auth->acl_get('m_report', $forum_id)) ? true : false,
				'U_MCP_REPORT'			=> ($this->auth->acl_get('m_report', $forum_id)) ? append_sid("{$this->root_path}mcp.{$this->php_ext}", 'i=reports&amp;mode=report_details&amp;f=' . $forum_id . '&amp;p=' . $row['post_id'], true, $this->user->session_id) : '',

				'MESSAGE'				=> $row['post_text'],

				'S_HAS_POLL'			=> (!empty($row['poll_start'])) ? true : false,
				'POLL_QUESTION'			=> htmlspecialchars(censor_text($row['poll_title']), ENT_QUOTES, 'UTF-8', false),
				'S_HAS_ATTACHMENTS'		=> (!empty($attachments[$row['post_id']]) && $this->config['news_attach_show']) ? true : false,
				'S_DISPLAY_NOTICE'		=> $display_notice && $row['post_attachment'],
				'EDITED_MESSAGE'		=> $l_edited_by,
				'EDIT_REASON'			=> htmlspecialchars($row['post_edit_reason'], ENT_QUOTES, 'UTF-8', false),
				'SIGNATURE'				=> ($row['enable_sig']) ? $row['user_sig'] : '',
				'NEWS_COMMENTS'			=> $comments,
				'NEWS_COMMENTS_TEXT'	=> $this->user->lang('NEWS_COMMENTS_COUNT', $comments),
				'S_NEWS_SHORTENED'		=> $shortened,
				'U_NEWS_REPLY'			=> ($this->auth->acl_get('f_reply', $forum_id) && ($row['topic_status'] != ITEM_LOCKED || $this->auth->acl_get('m_edit', $forum_id))) ? append_sid("{$this->root_path}posting.{$this->php_ext}", "mode=reply&amp;t=$topic_id") : '',
				'NEWS_LEAD_IMAGE_SRC'	=> utf8_htmlspecialchars($lead_image['src']),
				'NEWS_LEAD_IMAGE_ALT'	=> utf8_htmlspecialchars($lead_image['alt']),
				'NEWS_FORUM_NAME'		=> $forum_name,
				'U_NEWS_FORUM'			=> $u_forum,
				'NEWS_IN_FORUM'			=> ($forum_name !== '') ? $this->user->lang('NEWS_IN_FORUM', '<a href="' . $u_forum . '">' . $forum_name . '</a>') : '',

				'POSTER_AVATAR'			=> ($this->user->optionget('viewavatars')) ? $this->get_poster_avatar($row) : '',
				'U_POST_AUTHOR'			=> get_username_string('profile', $poster_id, $row['username'], $row['user_colour'], $row['post_username']),
				'RANK_TITLE'			=> $row['rank_title'],
				'RANK_IMG'				=> $row['rank_image'],
				'RANK_IMG_SRC'			=> $row['rank_image_src'],
				'POSTER_POSTS'			=> $row['user_posts'],
				'U_SEARCH'				=> $this->auth->acl_get('u_search') ? append_sid("{$this->root_path}search.{$this->php_ext}", 'author_id=' . $poster_id . '&amp;sr=posts') : '',
			));

			if (!empty($profile_fields[$poster_id]))
			{
				$cp_row = $this->profile_fields->generate_profile_fields_template_data($profile_fields[$poster_id], false);
				foreach ($cp_row['blockrow'] as $field_data)
				{
					$this->template->assign_block_vars('postrow.custom_fields', $field_data);
				}
			}

			// Display not already displayed Attachments for this post, we already parsed them. ;)
			if (!empty($attachments[$post_id]))
			{
				foreach ($attachments[$post_id] as $attachment)
				{
					$this->template->assign_block_vars('postrow.attachment', array(
						'DISPLAY_ATTACHMENT'	=> $attachment,
					));
				}
			}
		}

		// Specify some images
		$this->template->assign_vars(array(
			'NEWS_USER_INFO'			=> (bool) $this->config['news_user_info'],
			'NEWS_POST_BUTTONS'			=> (bool) $this->config['news_post_buttons'],

			'NEWS_ONLY'					=> $this->news,
			'NEWS_TITLE'				=> $this->get_page_title(),
		));

		return;
	}

	/**
	 * Whether a post shows the "last edited by" notice, as viewtopic.php decides it.
	 *
	 * @param array $row Post row
	 * @return bool
	 */
	protected function shows_edit_notice(array $row)
	{
		return ($row['post_edit_count'] && $this->config['display_last_edited']) || $row['post_edit_reason'];
	}

	/**
	 * Names and colours of the users who last edited the shown posts, when that
	 * was not the poster, in one query.
	 *
	 * @param array $rows Post rows
	 * @return array User rows keyed by user ID
	 */
	protected function get_editors(array $rows)
	{
		$editor_ids = array();
		foreach ($rows as $row)
		{
			if ($this->shows_edit_notice($row) && $row['post_edit_user'] && $row['post_edit_user'] != $row['poster_id'])
			{
				$editor_ids[] = (int) $row['post_edit_user'];
			}
		}

		$editors = array();
		if (empty($editor_ids))
		{
			return $editors;
		}

		$sql = 'SELECT user_id, username, user_colour
			FROM ' . USERS_TABLE . '
			WHERE ' . $this->db->sql_in_set('user_id', array_unique($editor_ids));
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$editors[(int) $row['user_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		return $editors;
	}

	/**
	 * The posters' profile fields that are set to show on topic pages, as viewtopic.php
	 * picks them.
	 *
	 * @param int[] $poster_ids
	 * @return array Profile fields keyed by user ID, then by field name
	 */
	protected function get_profile_fields(array $poster_ids)
	{
		if (empty($poster_ids) || !$this->config['load_cpf_viewtopic'])
		{
			return array();
		}

		$profile_fields = array();
		foreach ($this->profile_fields->grab_profile_fields_data(array_unique($poster_ids)) as $user_id => $fields)
		{
			foreach ($fields as $ident => $field)
			{
				if ($field['data']['field_show_on_vt'])
				{
					$profile_fields[$user_id][$ident] = $field;
				}
			}
		}

		return $profile_fields;
	}

	/**
	 * @param array $post_ids
	 * @return array
	 */
	public function get_attachments(array $post_ids)
	{
		$attachments = array();

		if ($this->auth->acl_get('u_download'))
		{
			$sql = 'SELECT *
				FROM ' . ATTACHMENTS_TABLE . '
				WHERE ' . $this->db->sql_in_set('post_msg_id', $post_ids) . '
					AND in_message = 0
				ORDER BY filetime DESC, post_msg_id ASC';
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$attachments[$row['post_msg_id']][] = $row;
			}
			$this->db->sql_freeresult($result);
		}

		return $attachments;
	}

	/**
	 * @return array
	 */
	public function get_newstopic_sql()
	{
		$sql_array = array(
			'SELECT'		=> 'forum_id, topic_id, topic_type, topic_poster, topic_first_post_id',
			'FROM'			=> array(TOPICS_TABLE => 't'),
			'WHERE'			=> $this->db->sql_in_set('forum_id', $this->get_forums($this->category), false, true) . '
				AND topic_visibility = ' . ITEM_APPROVED,
			'ORDER_BY'		=> 'topic_time DESC',
		);

		if ($this->news)
		{
			$sql_array['WHERE'] .= ' AND topic_id = ' . $this->news;
			$sql_array['ORDER_BY'] = '';
		}
		else if ($this->is_archive())
		{
			$archive_start = $this->user->get_timestamp_from_format('Y-n-d H:i:s', $this->archive['y'] . '-' . $this->archive['m'] . '-01 0:00:00');
			$next_month = $this->archive['m'] == 12 ? 1 : $this->archive['m'] + 1;
			$next_year = $this->archive['y'] + ($this->archive['m'] == 12 ? 1 : 0);
			$archive_end = $this->user->get_timestamp_from_format('Y-n-d H:i:s', $next_year . '-' . $next_month . '-01 0:00:00') - 1;
			$archive_name = $this->user->lang('NEWS_ARCHIVE_OF', $this->user->format_date($archive_start, 'F Y'));

			$this->page_title = $archive_name;
			$sql_array['WHERE'] .= ' AND topic_time >= ' . (int) $archive_start . ' AND topic_time <= ' . (int) $archive_end;
			$sql_array['ORDER_BY'] = 'topic_time ASC';
		}

		if (!$this->news && !$this->config['news_shadow'])
		{
			$sql_array['WHERE'] .= ' AND topic_moved_id = 0';
		}

		return $sql_array;
	}

	/**
	* Get a list of the forums we use for the newspage
	*
	* @param	int		$limit_category		Limit the list to a given category
	* @return	array		Array with the forum ids that should be taken into account
	*/
	protected function get_forums($limit_category = 0)
	{
		static $_forums;

		if (isset($_forums[$limit_category]))
		{
			return $_forums[$limit_category];
		}

		$forums = array_map('intval', explode(',', ($this->config['news_forums'] ?: '0')));

		$forum_read_ary = array();

		// Do not include those forums the user is not having read access to...
		$forum_ary = $this->auth->acl_getf('f_read');
		foreach ($forum_ary as $forum_id => $allowed)
		{
			if ($allowed['f_read'])
			{
				$forum_read_ary[] = (int) $forum_id;
			}
		}

		// Remove duplicates
		$forum_read_ary = array_unique($forum_read_ary);

		// Now only keep the forums that are in "allowed to read", "news forum" list
		$_forums[$limit_category] = array_intersect($forum_read_ary, $forums);

		if ($limit_category)
		{
			$_forums[$limit_category] = array_intersect($_forums[$limit_category], array($limit_category));
		}

		return $_forums[$limit_category];
	}

	/**
	* Generate the category list of the news forums and populate it into the template
	*/
	public function generate_category_list()
	{
		$sql = 'SELECT forum_id, forum_name, forum_topics_approved
			FROM ' . FORUMS_TABLE . '
			WHERE ' . $this->db->sql_in_set('forum_id', $this->get_forums(), false, true) . '
				AND forum_topics_approved <> 0
			ORDER BY left_id ASC';
		$result = $this->db->sql_query($sql);

		/** @var \phpbbmodders\newspage\route $route */
		$route = $this->news_helper->generate_route($this->category, $this->archive);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$u_post = '';
			if ($this->auth->acl_get('f_post', $row['forum_id']) || $this->user->data['user_id'] == ANONYMOUS)
			{
				$u_post = append_sid("{$this->root_path}posting.{$this->php_ext}", 'mode=post&amp;f=' . $row['forum_id']);
			}
			$this->template->assign_block_vars('cat_block', array(
				'U_NEWS_CAT'		=> $route->get_url($row['forum_id'], ($this->category == $row['forum_id']) ? '' : false),
				'NEWS_CAT'			=> $row['forum_name'],
				'NEWS_COUNT'		=> $row['forum_topics_approved'],
				'S_SELECTED'		=> $this->category == $row['forum_id'],
				'U_POST_NEWS'		=> $u_post,
			));

			if ($this->category == $row['forum_id'])
			{
				$this->template->assign_vars(array(
					'NEWS_FILTER_CATEGORY'		=> $row['forum_name'],
					'U_REMOVE_CATEGORY_FILTER'	=> $route->get_url(true, ($this->category == $row['forum_id']) ? '' : false),
				));
			}
		}
		$this->db->sql_freeresult($result);
	}

	/**
	* Generate the archive list of the news forums and populate it into the template
	*
	* @return	null
	*/
	public function generate_archive_list()
	{
		$this->num_pagination_items = 0;

		$archiv_years = $archiv_months = $checked_months = array();
		$sql = 'SELECT topic_time
			FROM ' . TOPICS_TABLE . '
			WHERE ' . $this->db->sql_in_set('forum_id', $this->get_forums($this->category), false, true) . '
				AND topic_visibility = ' . ITEM_APPROVED . '
			ORDER BY topic_time DESC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$year = $this->user->format_date($row['topic_time'], 'Y');
			$month = $this->user->format_date($row['topic_time'], 'm');

			if (!isset($archiv_months[$year]))
			{
				$archiv_years[$year] = 0;
			}

			if (isset($archiv_months[$year][$month]))
			{
				$archiv_years[$year]++;
				$archiv_months[$year][$month]['count']++;
				continue;
			}

			$archiv_months[$year][$month] = array(
				'url'	=> $this->user->format_date($row['topic_time'], 'Y/m'),
				'name'	=> $this->user->format_date($row['topic_time'], 'F'),
				'count'	=> 1,
			);
		}
		$this->db->sql_freeresult($result);

		$this->assign_archive_list($archiv_years, $archiv_months);
	}

	/**
	 * Assign the archive blocks to the template
	 *
	 * @param array $archiv_years
	 * @param array $archiv_months
	 */
	protected function assign_archive_list(array $archiv_years, array $archiv_months)
	{
		foreach ($archiv_years as $year => $news)
		{
			$this->template->assign_block_vars('archive_block', array(
				'NEWS_YEAR'		=> $year,
			));

			foreach ($archiv_months[$year] as $month => $archive)
			{
				$active_archive = false;
				if ($this->is_paginated_archive($year, $month))
				{
					$active_archive = $this->is_current_archive($year, $month);
					$this->num_pagination_items += $archive['count'];
				}

				/** @var \phpbbmodders\newspage\route $route */
				$route = $this->news_helper->generate_route($this->category, $this->archive);
				$this->template->assign_block_vars('archive_block.archive_row', array(
					'U_NEWS_MONTH'		=> $route->get_url(($active_archive) ? '' : empty($this->config['news_cat_show']), $archive['url']),
					'NEWS_MONTH'		=> $archive['name'],
					'NEWS_COUNT'		=> $archive['count'],
					'S_SELECTED'		=> $active_archive,
				));

				if ($active_archive)
				{
					$this->template->assign_vars(array(
						'NEWS_FILTER_ARCHIVE_YEAR'		=> $year,
						'NEWS_FILTER_ARCHIVE_MONTH'		=> $archive['name'],
						'U_REMOVE_ARCHIVE_FILTER'		=> $route->get_url(($active_archive) ? '' : empty($this->config['news_cat_show']), true),
					));
				}
			}
		}

		return;
	}

	/**
	* Counts the total number of news for pagination
	*
	* @return	null
	*/
	public function count_num_pagination_items()
	{
		$this->num_pagination_items = 0;

		$sql = 'SELECT COUNT(topic_id) AS num_news
			FROM ' . TOPICS_TABLE . '
			WHERE ' . $this->db->sql_in_set('forum_id', $this->get_forums($this->category), false, true) . '
				AND topic_visibility = ' . ITEM_APPROVED;
		$result = $this->db->sql_query($sql);
		$this->num_pagination_items = $this->db->sql_fetchfield('num_news');
		$this->db->sql_freeresult($result);

		return;
	}

	/**
	* Generate the pagination for the news list
	*
	* @return	null
	*/
	public function generate_pagination()
	{
		if (!$this->is_archive())
		{
			$max_num_news = $this->config['news_pages'] * $this->config['news_number'];
			$pagination_news = min($max_num_news, $this->num_pagination_items);
		}
		else
		{
			$pagination_news = $this->num_pagination_items;
		}

		/** @var \phpbbmodders\newspage\route $route */
		$route = $this->news_helper->generate_route($this->category, $this->archive);
		$this->pagination->generate_template_pagination(
			array(
				'routes' => array(
					$route->get_route(),
					$route->get_route(false, false, 2),
				),
				'params' => $route->get_params(),
			), 'pagination', 'page', $pagination_news, $this->config['news_number'], $this->start);

		$this->template->assign_vars(array(
			'PAGE_NUMBER'		=> $this->pagination->on_page($pagination_news, $this->config['news_number'], $this->start),
			'TOTAL_NEWS'		=> $this->user->lang('VIEW_NEWS_POSTS', $this->num_pagination_items),
		));
	}

}

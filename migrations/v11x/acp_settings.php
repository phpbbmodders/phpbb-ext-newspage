<?php
/**
 *
 * NV Newspage extension for the phpBB Forum Software package
 *
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\newspage\migrations\v11x;

class acp_settings extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return array('\\phpbbmodders\\newspage\\migrations\\v11x\\better_archive_config_option');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('news_icon', 'fa-bullhorn')),
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_NEWSPAGE')),
			array('module.add', array('acp', 'ACP_NEWSPAGE', array(
				'module_basename' => '\\phpbbmodders\\newspage\\acp\\main_module',
				'modes' => array('settings'),
			))),
		);
	}
}

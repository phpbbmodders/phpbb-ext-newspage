<?php

namespace nickvergessen\newspage\migrations\v11x;

class acp_settings extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\\nickvergessen\\newspage\\migrations\\v11x\\better_archive_config_option');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('news_icon', 'fa-bullhorn')),
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_NEWSPAGE')),
			array('module.add', array('acp', 'ACP_NEWSPAGE', array(
				'module_basename' => '\\nickvergessen\\newspage\\acp\\main_module',
				'modes' => array('settings'),
			))),
		);
	}
}

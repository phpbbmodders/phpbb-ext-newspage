<?php
/**
 *
 * NV Newspage extension for the phpBB Forum Software package
 *
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\newspage\migrations\v14x;

/**
 * Adds the setting that shows each news post's first image above its text. Off by
 * default, so existing boards look the same until an administrator turns it on.
 */
class lead_image extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['news_lead_image']);
	}

	public static function depends_on()
	{
		return array('\\phpbbmodders\\newspage\\migrations\\v11x\\acp_settings');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('news_lead_image', 0)),
		);
	}
}

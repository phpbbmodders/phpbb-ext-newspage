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

namespace phpbbmodders\newspage\migrations\v11x;

use phpbb\db\migration\migration;

/**
 * @package phpbbmodders\newspage\migrations\v11x
 */
class better_archive_config_option extends migration
{
	/**
	 * {@inheritdoc}
	 */
	public function effectively_installed()
	{
		return !isset($this->config['news_archive_per_year']) && isset($this->config['news_archive_show']);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function depends_on()
	{
		return array('\phpbbmodders\newspage\migrations\v11x\release_1_1_0');
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_data()
	{
		return array(
			array('config.add', array('news_archive_show', $this->config['news_archive_per_year'])),
			array('config.remove', array('news_archive_per_year', 1)),
		);
	}
}

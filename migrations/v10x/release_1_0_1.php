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

namespace phpbbmodders\newspage\migrations\v10x;

use phpbb\db\migration\migration;

/**
 * @package phpbbmodders\newspage\migrations\v10x
 */
class release_1_0_1 extends migration
{
	/**
	 * {@inheritdoc}
	 */
	public function effectively_installed()
	{
		return version_compare($this->config['newspage_mod_version'], '1.0.1', '>=');
	}

	/**
	 * {@inheritdoc}
	 */
	public static function depends_on()
	{
		return array('\phpbbmodders\newspage\migrations\v10x\release_1_0_0');
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_data()
	{
		return array(
			array('config.add', array('news_pages', 1)),
			array('config.update', array('newspage_mod_version', '1.0.1')),
		);
	}
}

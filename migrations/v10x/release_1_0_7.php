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
class release_1_0_7 extends migration
{
	/**
	 * {@inheritdoc}
	 */
	public function effectively_installed()
	{
		return version_compare($this->config['newspage_mod_version'], '1.0.7', '>=');
	}

	/**
	 * {@inheritdoc}
	 */
	public static function depends_on()
	{
		return array('\phpbbmodders\newspage\migrations\v10x\release_1_0_6');
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_data()
	{
		return array(
			array('config.add', array('news_shadow', 0)),

			array('config.update', array('newspage_mod_version', '1.0.7')),
		);
	}
}

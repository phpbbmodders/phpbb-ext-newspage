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
class replace_acp_module_with_controller extends migration
{
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
			array('if', array(
				array('module.exists', array('acp', 'ACP_NEWSPAGE_TITLE', 'ACP_NEWSPAGE_CONFIG')),
				array('module.remove', array('acp', 'ACP_NEWSPAGE_TITLE', 'ACP_NEWSPAGE_CONFIG')),
			)),

			array('if', array(
				array('module.exists', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_NEWSPAGE_TITLE')),
				array('module.remove', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_NEWSPAGE_TITLE')),
			)),
		);
	}
}

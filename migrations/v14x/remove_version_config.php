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
 * Removes the version number the 1.0 and 1.1 migrations kept in the board config.
 * phpBB reads an extension's version from its composer.json, so the copy is not needed.
 */
class remove_version_config extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return !isset($this->config['newspage_mod_version']);
	}

	public static function depends_on()
	{
		return array('\\phpbbmodders\\newspage\\migrations\\v11x\\release_1_1_1');
	}

	public function update_data()
	{
		return array(
			array('config.remove', array('newspage_mod_version')),
		);
	}
}

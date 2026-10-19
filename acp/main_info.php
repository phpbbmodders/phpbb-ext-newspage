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

class main_info
{
	public function module()
	{
		return array(
			'filename' => '\\phpbbmodders\\newspage\\acp\\main_module',
			'title' => 'ACP_NEWSPAGE',
			'modes' => array(
				'settings' => array(
					'title' => 'ACP_NEWSPAGE_SETTINGS',
					'auth' => 'ext_phpbbmodders/newspage && acl_a_board',
					'cat' => array('ACP_NEWSPAGE'),
				),
			),
		);
	}
}

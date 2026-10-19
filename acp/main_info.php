<?php

namespace nickvergessen\newspage\acp;

class main_info
{
	public function module()
	{
		return array(
			'filename' => '\\nickvergessen\\newspage\\acp\\main_module',
			'title' => 'ACP_NEWSPAGE',
			'modes' => array(
				'settings' => array(
					'title' => 'ACP_NEWSPAGE_SETTINGS',
					'auth' => 'ext_nickvergessen/newspage && acl_a_board',
					'cat' => array('ACP_NEWSPAGE'),
				),
			),
		);
	}
}

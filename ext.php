<?php

namespace nickvergessen\newspage;

class ext extends \phpbb\extension\base
{
	public function is_enableable()
	{
		if (phpbb_version_compare(PHPBB_VERSION, '3.3.19', '>=') &&
			phpbb_version_compare(PHPBB_VERSION, '4.0.0-dev', '<') &&
			PHP_VERSION_ID >= 70200 && extension_loaded('dom'))
		{
			return true;
		}

		$language = $this->container->get('language');
		$language->add_lang('install', 'nickvergessen/newspage');
		return array($language->lang('NEWSPAGE_NOT_ENABLEABLE'));
	}
}

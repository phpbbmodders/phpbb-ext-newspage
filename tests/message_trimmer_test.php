<?php
/**
 *
 * NV Newspage extension for the phpBB Forum Software package
 *
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\newspage\tests;

use phpbbmodders\newspage\message_trimmer;

/**
 * Testing \phpbbmodders\newspage\message_trimmer
 */
class message_trimmer_test extends \PHPUnit\Framework\TestCase
{
	/**
	 * Rendered post text, the length to shorten it to, and the expected result.
	 *
	 * @return array
	 */
	public function trim_data()
	{
		return array(
			'0 keeps everything'			=> array('<strong>abc</strong>', 0, '<strong>abc</strong>'),
			'exact length is kept'			=> array('<strong>abc</strong>', 3, '<strong>abc</strong>'),
			'longer text is shortened'		=> array('<strong>abcdef</strong><em>tail</em>', 3, '<strong>abc</strong>...'),
			'entities count as one'			=> array('<p>A &amp; B</p><p>tail</p>', 3, '<p>A &amp;</p>...'),
			'links keep their attributes'	=> array('<a href="https://example.com">abcdef</a>', 3, '<a href="https://example.com">abc</a>...'),
			'multibyte characters'			=> array('<p>' . "\xc3\xa9\xf0\x9f\x98\x80" . 'tail</p>', 2, '<p>' . "\xc3\xa9\xf0\x9f\x98\x80" . '</p>...'),
			'nested tags are closed'		=> array('<strong>a<em>bcdef</em></strong>', 3, '<strong>a<em>bc</em></strong>...'),
			'images after the cut are dropped'	=> array('<p>abc</p><img src="example.png" alt=""><p>def</p>', 3, '<p>abc</p>...'),
		);
	}

	/**
	 * @dataProvider trim_data
	 *
	 * @param string $html     Rendered post text
	 * @param int    $length   Characters to keep
	 * @param string $expected Shortened text
	 */
	public function test_trim($html, $length, $expected)
	{
		$this->assertSame($expected, (new message_trimmer())->trim($html, $length));
	}
}

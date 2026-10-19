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

use phpbbmodders\newspage\lead_image;

/**
 * Testing \phpbbmodders\newspage\lead_image
 */
class lead_image_test extends \PHPUnit\Framework\TestCase
{
	/**
	 * Rendered post text, and the text, image address and text expected back.
	 *
	 * @return array
	 */
	public function take_data()
	{
		return array(
			'no image' => array(
				'<p>Just text.</p>',
				'<p>Just text.</p>', '', '',
			),
			'smilies are not images' => array(
				'Hello <img class="smilies" src="images/smilies/icon_e_smile.gif" alt=":)"> there',
				'Hello <img class="smilies" src="images/smilies/icon_e_smile.gif" alt=":)"> there', '', '',
			),
			'first BBCode image' => array(
				'<p>Before</p><img src="https://example.com/a.png" class="postimage" alt="Image"><p>After</p><img src="https://example.com/b.png" class="postimage" alt="Image">',
				'<p>Before</p><p>After</p><img src="https://example.com/b.png" class="postimage" alt="Image">', 'https://example.com/a.png', 'Image',
			),
			'linked image loses its link' => array(
				'<a href="https://example.com/big.png"><img src="https://example.com/a.png" class="postimage" alt="Image"></a> Text',
				'Text', 'https://example.com/a.png', 'Image',
			),
			'inline attachment block is removed' => array(
				'Intro <div class="inline-attachment"><dl class="file"><dt class="attach-image"><img src="./download/file.php?id=1" class="postimage" alt="The image"></dt><dd>The image</dd><dd>pixel.png (69 Bytes)</dd></dl></div> Outro',
				'Intro  Outro', './download/file.php?id=1', 'The image',
			),
			'line breaks after the image are dropped' => array(
				'<img src="https://example.com/a.png" class="postimage" alt="Image"><br>' . "\n" . '<br>' . "\n" . 'Thanks to everyone.<br>Second line.',
				'Thanks to everyone.<br>Second line.', 'https://example.com/a.png', 'Image',
			),
			'entities in the address are decoded once' => array(
				'<img src="./download/file.php?id=1&amp;sid=abc" class="postimage" alt="Pic">',
				'', './download/file.php?id=1&sid=abc', 'Pic',
			),
		);
	}

	/**
	 * @dataProvider take_data
	 *
	 * @param string $html     Rendered post text
	 * @param string $expected Text without the image
	 * @param string $src      Image address
	 * @param string $alt      Image text
	 */
	public function test_take($html, $expected, $src, $alt)
	{
		$taken = (new lead_image())->take($html);

		$this->assertSame($expected, $taken['html']);
		$this->assertSame($src, $taken['src']);
		$this->assertSame($alt, $taken['alt']);
	}
}

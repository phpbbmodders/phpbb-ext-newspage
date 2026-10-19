<?php
/**
 *
 * NV Newspage extension for the phpBB Forum Software package
 *
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\newspage;

/**
 * Takes the first image out of a rendered post, so the news list can show it
 * above the text instead of inside it.
 */
class lead_image
{
	/**
	 * Find the first post image (a BBCode image or an image attachment) and remove it.
	 *
	 * Smilies and other images without phpBB's "postimage" class are skipped. An
	 * attachment image is removed with its whole attachment block (comment and
	 * file size), a linked image with its link.
	 *
	 * @param string $html Safe HTML returned by phpBB's message renderer
	 * @return array 'html': the HTML without the image; 'src' and 'alt': the
	 *               image's address and text, both '' when there is no image
	 */
	public function take($html)
	{
		$none = array('html' => $html, 'src' => '', 'alt' => '');
		if ($html === '' || stripos($html, 'postimage') === false)
		{
			return $none;
		}

		$document = new \DOMDocument();
		$previous = libxml_use_internal_errors(true);
		$loaded = $document->loadHTML('<?xml encoding="UTF-8"><div>' . $html . '</div>', LIBXML_NONET);
		libxml_clear_errors();
		libxml_use_internal_errors($previous);
		if (!$loaded)
		{
			return $none;
		}

		$wrapper = $document->getElementsByTagName('div')->item(0);
		$image = null;
		foreach ($wrapper->getElementsByTagName('img') as $candidate)
		{
			if (in_array('postimage', preg_split('/\s+/', $candidate->getAttribute('class')), true) && $candidate->getAttribute('src') !== '')
			{
				$image = $candidate;
				break;
			}
		}
		if ($image === null)
		{
			return $none;
		}

		$src = $image->getAttribute('src');
		$alt = $image->getAttribute('alt');
		$this->remove_image($image, $wrapper);

		// Line breaks that came after the image would now start the text
		while ($wrapper->firstChild !== null && ($wrapper->firstChild->nodeName === 'br' || ($wrapper->firstChild instanceof \DOMText && trim($wrapper->firstChild->nodeValue) === '')))
		{
			$wrapper->removeChild($wrapper->firstChild);
		}

		$result = '';
		foreach ($wrapper->childNodes as $node)
		{
			$result .= $document->saveHTML($node);
		}

		return array('html' => trim($result), 'src' => $src, 'alt' => $alt);
	}

	/**
	 * Remove the image together with the block that only exists to hold it.
	 *
	 * @param \DOMElement $image   The image
	 * @param \DOMElement $wrapper The element around the whole post
	 * @return void
	 */
	protected function remove_image(\DOMElement $image, \DOMElement $wrapper)
	{
		// An attachment: drop its whole block, and an inline-attachment wrapper around it
		$remove = null;
		for ($node = $image->parentNode; $node !== null && !$node->isSameNode($wrapper); $node = $node->parentNode)
		{
			$classes = $node instanceof \DOMElement ? preg_split('/\s+/', $node->getAttribute('class')) : array();
			if (in_array('inline-attachment', $classes, true) || ($node->nodeName === 'dl' && in_array('file', $classes, true)))
			{
				$remove = $node;
			}
		}

		// A linked image: drop the link too
		if ($remove === null)
		{
			$parent = $image->parentNode;
			$remove = ($parent->nodeName === 'a' && trim($parent->textContent) === '') ? $parent : $image;
		}

		$remove->parentNode->removeChild($remove);
	}
}

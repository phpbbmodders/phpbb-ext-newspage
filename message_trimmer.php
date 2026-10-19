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

/** Trims rendered messages without cutting HTML tags, entities, or UTF-8 characters. */
class message_trimmer
{
	/**
	 * @param string $html Safe HTML returned by phpBB's message renderer.
	 * @param int $limit Maximum number of text characters; zero disables trimming.
	 * @return string A balanced HTML fragment with an ellipsis when shortened.
	 */
	public function trim($html, $limit)
	{
		if ($limit <= 0 || $html === '')
		{
			return $html;
		}

		$document = new \DOMDocument();
		$previous = libxml_use_internal_errors(true);
		$loaded = $document->loadHTML('<?xml encoding="UTF-8"><div>' . $html . '</div>', LIBXML_NONET);
		libxml_clear_errors();
		libxml_use_internal_errors($previous);
		if (!$loaded)
		{
			return $html;
		}

		$wrapper = $document->getElementsByTagName('div')->item(0);
		if (utf8_strlen($wrapper->textContent) <= $limit)
		{
			return $html;
		}

		$remaining = $limit;
		$this->trim_children($wrapper, $remaining);
		$wrapper->appendChild($document->createTextNode('...'));
		$result = '';
		foreach ($wrapper->childNodes as $node)
		{
			$result .= $document->saveHTML($node);
		}
		return $result;
	}

	protected function trim_children(\DOMNode $parent, &$remaining)
	{
		foreach (iterator_to_array($parent->childNodes) as $node)
		{
			if ($remaining === 0)
			{
				$parent->removeChild($node);
			}
			else if ($node instanceof \DOMText)
			{
				$length = utf8_strlen($node->nodeValue);
				$node->nodeValue = utf8_substr($node->nodeValue, 0, $remaining);
				$remaining -= min($remaining, $length);
			}
			else if ($node->hasChildNodes())
			{
				$this->trim_children($node, $remaining);
			}
		}
	}
}

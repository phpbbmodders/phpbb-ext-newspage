# NV Newspage

[![Tests](https://github.com/phpbbmodders/phpbb-ext-newspage/actions/workflows/tests.yml/badge.svg)](https://github.com/phpbbmodders/phpbb-ext-newspage/actions/workflows/tests.yml) [![Lint](https://github.com/phpbbmodders/phpbb-ext-newspage/actions/workflows/lint.yml/badge.svg)](https://github.com/phpbbmodders/phpbb-ext-newspage/actions/workflows/lint.yml)

A news page that shows the latest topics from chosen forums, shortened to a set length, with monthly archives and categories.

## Features

- A **News** page at `/news`, linked from the board's navigation bar with an icon you choose.
- Shows the first post of the newest topics in the forums you pick, with a set number of news per page and pages.
- Shortens long news to a set number of characters, with a "Read full news" link. Formatting is kept and no tag is cut in half.
- Visitors can filter the news by forum and by month, and open a single news item.
- Optional poster information (avatar, rank, profile fields), post buttons, attachments and moved topics.
- Shows in **Who is online** when someone is reading the news.
- Settings under **ACP → Extensions → Newspage**.

## Screenshots

<table>
<tr>
<td><a href="docs/images/screenshots/newspage-news.png"><img src="docs/images/screenshots/newspage-news.png" width="440" alt="News page"></a></td>
<td><a href="docs/images/screenshots/newspage-acp-settings.png"><img src="docs/images/screenshots/newspage-acp-settings.png" width="440" alt="ACP settings"></a></td>
</tr>
</table>

Click an image for the full size. See the [screenshot gallery](docs/images/screenshots/README.md) for the rest.

## Requirements

- phpBB 3.3.19 or later, including phpBB 4.0 (tested on its 4.0.0-a3 development version)
- PHP 7.4 or later (phpBB 4.0 itself needs PHP 8.2 or later), with the DOM extension

## Installation

1. Copy the extension to `/ext/phpbbmodders/newspage`
2. In the Administration Control Panel, go to **Customise → Manage extensions**
3. Enable the **NV Newspage** extension
4. Choose the news forums under **ACP → Extensions → Newspage → Settings**

### Upgrading from `nickvergessen/newspage`

This extension used to be installed as `nickvergessen/newspage`. Your settings carry over:

1. Disable the old **NV Newspage** extension in the ACP. Do **not** delete its data.
2. Upload this version to `/ext/phpbbmodders/newspage` and enable it. The old install's migration history is moved to the new name automatically.
3. Delete the `/ext/nickvergessen/newspage` folder and purge the board cache.

If you disable the old extension from the command line (`bin/phpbbcli.php`) instead of the ACP, run `bin/phpbbcli.php cache:purge` before enabling the new one; the command-line disable doesn't clear the cache.

The settings moved from the news page itself to the ACP. The News link's icon is a Font Awesome class: phpBB 3.3 uses Font Awesome 4.7 and phpBB 4.0 uses Font Awesome 6, so pick a name both have (the default, `fa-bullhorn`, works on both).

## Events

The PHP and template events other extensions can use are listed in [docs/events.md](docs/events.md).

## TODO

Ideas not yet built, practical and speculative alike: [`docs/TODO.md`](docs/TODO.md).

## Contributing

Contributions are welcome!

- **Bug reports**: [Open an issue](https://github.com/phpbbmodders/phpbb-ext-newspage/issues).
- **Everything else** (questions, feature requests, ideas, general discussion): [Use Discussions](https://github.com/orgs/phpbbmodders/discussions), or the [community forum](https://www.phpbbmodders.com/community/).
- Pull requests are welcome for bug fixes or discussed features.

## Acknowledgments

- Based on [nickv-phpbb/phpbb-ext-newspage](https://github.com/nickv-phpbb/phpbb-ext-newspage), originally developed by Joas Schilling ([nickvergessen](https://github.com/nickvergessen)). Original history, authorship and licensing are preserved.
- Code review, bug fixes, and documentation assisted by [Claude](https://www.anthropic.com/claude).
- Code review and documentation assisted by [Codex](https://openai.com/codex/).

## License

This extension is licensed under the **GNU General Public License v2.0**.

See [license.txt](license.txt) for more information.

# NV Newspage Extension

An independent continuation of NV Newspage for [phpBB](https://www.phpbb.com/), maintained under phpbbmodders.

## Compatibility

The imported code targets phpBB 3.1. Work toward phpBB 3.3 support is planned; compatibility has not yet been implemented or verified. Do not install this development branch on a production board.

The original branches and release tags are preserved as historical upstream snapshots. They do not represent phpbbmodders releases or phpBB 3.3 support.


## Description

Adds an extra page to the board where a switchable number of news can be displayed.
The news texts can be shorten to a given number of characters.
Also the icons can be switched on/off (post icons, user icons)


## Development Installation

Clone into phpBB/ext/phpbbmodders/newspage:

    git clone https://github.com/phpbbmodders/phpbb-ext-newspage.git phpBB/ext/phpbbmodders/newspage

Set up the dependencies:

    php composer.phar install --dev

Go to "ACP" > "Customise" > "Extensions" and enable the "phpBB 3.1 NV Newspage Extension" extension.

Configure Newspage under "ACP" > "Extensions" > "Newspage" > "Settings". Existing news settings are preserved when upgrading.
The News navigation link defaults to the Font Awesome megaphone (`fa-bullhorn`). Its icon class can be changed on the same settings page.


## List of events

See the [documentation](docs/events.md) for a list of available php and template events.


## Testing

GitHub Actions checks PHP syntax. This does not establish phpBB runtime compatibility. The imported PHPUnit suite and Travis configuration target phpBB 3.1 and need updating before they can validate the 3.3 port.

## TODO

Ideas not yet built, practical and speculative alike: [`docs/TODO.md`](docs/TODO.md).

## Contributing

Contributions are welcome!

- **Bug reports**: [Open an issue](https://github.com/phpbbmodders/phpbb-ext-newspage/issues).
- **Everything else** (questions, feature requests, ideas, general discussion): [Use Discussions](https://github.com/phpbbmodders/phpbb-ext-newspage/discussions).
- Pull requests are welcome for bug fixes or discussed features.

## Acknowledgments

- Based on [nickv-phpbb/phpbb-ext-newspage](https://github.com/nickv-phpbb/phpbb-ext-newspage), originally developed by Joas Schilling (nickvergessen). Original history, authorship, and licensing are preserved.
- Code review and documentation assisted by [Codex](https://www.anthropic.com/Codex).


## License

[GPLv2](license.txt)

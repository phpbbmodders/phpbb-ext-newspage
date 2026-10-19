# TODO

- **German ACP and install strings**: `language/de/` only has `newspage.php`. The ACP settings page
  (`info_acp_newspage.php`) and the enable messages (`install_newspage.php`) fall back to English.
- **Version check**: no phpBB.com listing for this extension could be confirmed. Once it has one, add an
  `extra.version-check` block to `composer.json` pointing at it, so the ACP can report new releases.

# TODO

## phpBB 3.3 Compatibility

- Choose the minimum supported phpBB 3.3 and PHP versions.
- Update Composer metadata and add an enforced extension enableability check.
- Quote service references and parameters in YAML for Symfony compatibility.
- Check the message-trimming dependency and news rendering against phpBB 3.3.
- Update prosilver templates and review output escaping.
- Update the imported test suite and add phpBB 3.3 runtime CI.
- Validate fresh install, upgrade, disable/re-enable, purge, permissions,
  settings, news filters, pagination, archives, and attachments on disposable boards.

Preserve existing migrations. Add new migrations only when needed.

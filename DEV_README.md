# Developer Documentation

## Table of Contents
1. [Testing Archiving](#testing-archiving)
2. [NPM Commands](#npm-commands)
3. [Testing and Code Coverage](#testing-and-code-coverage)
   - [Running PHPUnit Tests](#running-phpunit-tests)
   - [Code Coverage Reports](#code-coverage-reports)
   - [Debugging Tests](#debugging-tests)
4. [Additional Resources](#additional-resources)

## Testing archiving:

To create a tarball of the `iiif_media_source` module for distribution or testing, use the following command:

```bash
git archive -o iiif_media_source.tar HEAD`
```

## npm commands
<!--
`npm run build` - Runs a build of css and js files.
`npm run watch` - Watches configured files for changes.
`npm run clean` - Deletes all files from dist folders.
-->

## Testing and Code Coverage

This section provides instructions for running PHPUnit tests and generating code coverage reports for the `iiif_media_source` module. These steps assume you are working in a Drupal project and have the necessary tools installed.

---

### Running PHPUnit Tests

The `iiif_media_source` module includes PHPUnit tests to ensure code quality and functionality. The tests are organized into groups for better management.

#### Current Test Groups:
- **iiif_media_source**: Tests for the core functionality of the `iiif_media_source` module.
- **iiif_image_style**: Tests for the `iiif_image_style` submodule.

#### Running Tests:
To run all tests for the module, use the following command:

```bash
ddev exec ./vendor/bin/phpunit ./web/modules/synced/Modules/iiif_media_source
```

To run a specific test group, use the --group option:

```bash
ddev exec ./vendor/bin/phpunit --group iiif_media_source ./web/modules/synced/Modules/iiif_media_source
```

---

### Code Coverage Reports

Code coverage reports help identify which parts of the codebase are covered by tests. Generating these reports requires xdebug to be enabled in your development environment.

#### Prerequisites:

1. Ensure xdebug is installed and enabled in your DDEV environment.
2. Follow the [DDEV xdebug documentation](https://docs.ddev.com/en/stable/users/debugging-profiling/step-debugging/#ide-setup) to configure your IDE and environment.

### Generating Code Coverage Reports:

Run the following command to generate an HTML code coverage report:

```bash
ddev exec XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html coverage-report ./web/modules/synced/Modules/iiif_media_source
```

This will create a coverage-report directory in your project root. Open the index.html file in a browser to view the report.

Notes

- The XDEBUG_MODE=coverage environment variable ensures that xdebug runs in coverage mode.
- Generating code coverage reports can be resource-intensive, so it’s recommended to run them selectively (e.g., for specific test groups).

---

### Debugging Tests

If a test fails, you can debug it by running PHPUnit in verbose mode:

```bash
ddev exec ./vendor/bin/phpunit --verbose ./web/modules/synced/Modules/iiif_media_source
```

To debug a specific test method, use the --filter option:

```bash
ddev exec ./vendor/bin/phpunit --filter testMethodName ./web/modules/synced/Modules/iiif_media_source
```

---

## Additional Resources

- DDEV Documentation: https://ddev.readthedocs.io/
- PHPUnit Documentation: https://phpunit.de/
- Drupal PHPUnit Testing: https://www.drupal.org/docs/automated-testing/phpunit-in-drupal

---

By following these steps, you can ensure that the `iiif_media_source` module is thoroughly tested and meets high-quality standards.

---

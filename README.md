# Freelo Translations

-----

## Features

- Extract strings (php and latte files extractor included) from source files
- Replace strings in source files with placeholders
- Store and automatically translates strings in database
- Generate dictionary files (mo files) for example

## Console commands

- `CheckTranslationsCommand` is executed in CI server to check if all strings are translated
- `CreateMoFilesCommand` creates all needed mo files for translator
- `ExtractAndReplaceTranslationsCommand` is used during development to extract strings, store in DB and replace with placeholders

## Contact

karel@freelo.cz

## License

MIT
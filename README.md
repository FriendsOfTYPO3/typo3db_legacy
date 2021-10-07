# TYPO3 Extension "typo3db_legacy" - Provides $GLOBALS['TYPO3_DB']

## The short story
Until TYPO3 core v8, the low level database API was provided by a class called
t3lib_db - later renamed to TYPO3\CMS\Core\Database\DatabaseConnection - and
populated in global scope as $GLOBALS['TYPO3_DB'].

The class managed connections and queries to mysql database endpoints and the
two additional extensions "dbal" and "adodb" were available to support further
different DBMS in TYPO3.

With TYPO3 v8 however, the database API was rewritten and is now based on
doctrine-dbal. All queries in the TYPO3 v8 core were rewritten to that new API,
keeping the old $GLOBALS['TYPO3_DB'] API working in parallel as backwards
compatible layer for extensions.

With TYPO3 v9, the old $GLOBALS['TYPO3_DB'] based database API has been extracted
from the core to this extension "typo3db_legacy". It provides the same functionality
as what has been supported by the core in v8 and can be used as a backwards
compatible layer for extensions that still did not move to the new doctrine based API.
So those extensions still work with TYPO3 v9.

## Installation
The latest version can be installed via TER (https://typo3.org) or via composer
by adding ''composer require friendsoftypo3/typo3db-legacy'' in a TYPO3 v9 installation.

If an extension should be compatible with both TYPO3 v8 and TYPO3 v9 and relies in v9
on typo3db_legacy, it should list typo3db_legacy as 'suggests' dependency in it's
ext_emconf.php file. This way, the dependency is optional and needs to be manually loaded
by an administrator in the TYPO3 v9 backend, but the core still ensures typo3db_legacy is loaded
before the affected extension:

```
    'constraints' => [
        ...
        'suggests' => [
            'typo3db_legacy' => '1.0.0-1.0.99',
        ],
    ],
```

Extensions that dropped support for TYPO3 v8 (or keeps separate branches) and did not migrate
to doctrine in its v9 version, should list typo3db_legacy in the 'depends' section of
ext_emconf.php:

```
    'constraints' => [
        ...
        'depends' => [
            'typo3db_legacy' => '1.0.0-1.1.99',
        ],
    ],
```

## Configuration
The extension consumes the same 'Default' configuration from TYPO3_CONF_VARS as the
doctrine based API. The connection is initialized in the extension's ext_localconf.php file.

## Current state
The latest version here reflects a feature-complete state. There are bugs, we know,
there are possible feature requests - we know. But it's highly likely that this
extension gets no new features, unless somebody steps up and continues the development
(see further below).

## Contribution
Feel free to submit any pull request, or add documentation, tests, as you please.
We will publish a new version every once in a while, depending on the amount of changes
and pull requests submitted.

If you want to keep adding features, and keep typo3db_legacy compatible with the latest
TYPO3 versions, feel free to contact Benni Mack.

## License
The extension is published under GPL v2+, all included third-party libraries are
published under their respective licenses.

## Authors
A lot of contributors have been working on this area while this functionality was part of
the TYPO3 Core. This package is now maintained by a loose group of TYPO3 enthusiasts inside
the TYPO3 Community. Feel free to contact Benni Mack (benni.mack@typo3.org) for any questions
regarding "typo3db_legacy".

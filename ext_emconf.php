<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3_DB compatibility layer for TYPO3 v9.x & TYPO3 v10.x',
    'description' => 'Provides $GLOBALS[\'TYPO3_DB\'] as backwards-compatibility with legacy functionality for extensions that haven\'t fully migrated to doctrine yet.',
    'category' => 'be',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'TYPO3 Community',
    'author_email' => '',
    'author_company' => '',
    'version' => '1.1.4',
    'constraints' => [
        'depends' => [
            'typo3' => '9.4.0-10.4.99'
        ],
    ],
];

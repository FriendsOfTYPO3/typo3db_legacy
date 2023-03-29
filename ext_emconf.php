<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3_DB compatibility layer',
    'description' => 'Provides $GLOBALS[\'TYPO3_DB\'] as backward compatibility with legacy functionality of extensions that are not yet fully migrated to doctrine.',
    'category' => 'be',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'TYPO3 Community',
    'author_email' => '',
    'author_company' => '',
    'version' => '1.2.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-12.4.99',
            'backend' => '10.4.0-12.4.99',
        ],
    ],
];

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
    'version' => '1.3.0',
    'constraints' => [
        'depends' => [
            'php' => '8.0.0-8.4.99',
            'typo3' => '12.4.0-13.4.99',
            'backend' => '10.4.0-13.4.99',
        ],
    ],
];

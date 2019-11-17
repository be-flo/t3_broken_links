<?php

/**
 * Extension Manager/Repository config file for ext "t3_broken_links".
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'T3 Broken Links',
    'description' => '',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
            'typo3' => '10.1.0-10.5.99',
            'fluid_styled_content' => '10.1.0-10.5.99',
            'rte_ckeditor' => '10.1.0-10.5.99'
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'BeFlo\\T3BrokenLinks\\' => 'Classes'
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Florian Peters',
    'author_email' => 'fpeters1392@googlemail.com',
    'author_company' => 'Be Flo',
    'version' => '1.0.0',
];

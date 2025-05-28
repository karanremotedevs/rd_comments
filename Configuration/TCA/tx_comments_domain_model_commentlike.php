<?php
$label = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:';
$once  = 'LLL:EXT:comments/Resources/Private/Language/locallang_db.xlf:tx_comments_domain_model_commentlike';

return [
    'ctrl' => [
        'title' => $once,
        'label' => 'comment_uid',
        'descriptionColumn' => 'comment_uid',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'hideTable' => true,
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY crdate DESC',
        'delete' => 'deleted',
        'iconfile' => 'EXT:comments/Resources/Public/Icons/plug_commentlike.svg',
        'security' => [
            'ignorePageTypeRestriction' => true
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'comment_uid, ip_address, crdate'],
    ],
    'columns' => [
        'tstamp' => [
            'exclude' => 1,
            'label' => $label . 'LGL.tstamp',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'crdate' => [
            'exclude' => 1,
            'label' => $label . 'LGL.crdate',
            'config' => [
                'type' => 'input',
                'size' => 12,
                'eval' => 'datetime',
                'readOnly' => true,
                'default' => 0,
            ],
        ],
        'deleted' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'comment_uid' => [
            'exclude' => 1,
            'label' => $once . '.comment_uid',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_comments_domain_model_comment',
                'minitems' => 0,
                'maxitems' => 1,
                'readOnly' => true,
            ],
        ],
       'ip_address' => [
            'exclude' => 1,
            'label' => $once . '.ip_address',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'readOnly' => true,
            ],
        ],
    ],
];

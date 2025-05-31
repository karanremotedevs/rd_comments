<?php
$label = 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:';
$once  = 'LLL:EXT:rd_comments/Resources/Private/Language/locallang_db.xlf:tx_rdcomments_domain_model_comment';
return [
    'ctrl' => [
        'title' => 'LLL:EXT:rd_comments/Resources/Private/Language/locallang_db.xlf:tx_rdcomments_domain_model_comment',
        'label' => 'username',
        'descriptionColumn' => 'username',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'hideTable' => true,
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'default_sortby' => 'ORDER BY uid DESC',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'description,newsuid,username,usermail,childcomment',
        'iconfile' => 'EXT:rd_comments/Resources/Public/Icons/plug_comment.svg',
        'security' => [
            'ignorePageTypeRestriction' => true
        ],
    ],
    'types' => [
        '1' => ['showitem' => ' username, usermail, description, terms, paramlink, childcomment, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, sys_language_uid, l10n_diffsource, hidden, starttime, endtime'],
    ],
    'columns' => [

        'sys_language_uid' => [
            'exclude' => true,
            'label' => $label . 'LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => $label . 'LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_rdcomments_domain_model_comment',
                'foreign_table_where' => 'AND tx_rdcomments_domain_model_comment.pid=###CURRENT_PID### AND tx_rdcomments_domain_model_comment.sys_language_uid IN (-1,0)',
            ],
        ],

        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        'hidden' => [
            'exclude' => 1,
            'label' => $label . 'LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => $label . 'LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                ],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => $label . 'LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                ],
            ],
        ],

        'newsuid' => [
            'exclude' => 1,
            'label' => $once . 'newsuid',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'internal_type' => 'db',
                'foreign_table' => 'tx_news_domain_model_news',
                'allowed' => 'tx_news_domain_model_news',
                'foreign_field' => 'comment',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'readOnly' => 1,
            ],
        ],

        'username' => [
            'exclude' => 1,
            'label' => $once . 'username',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => 1,
                'eval' => 'trim',
            ],
        ],
        'usermail' => [
            'exclude' => 1,
            'label' => $once . 'usermail',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '256',
                'eval' => 'trim',
                'wizards' => [
                    'link' => [
                        'type' => 'popup',
                        'title' => $once . 'linkTitle',
                        'icon' => 'link_popup.gif',
                        'module' => [
                            'name' => 'wizard_link',
                        ],
                        'JSopenParams' => 'height=800,width=600,status=0,menubar=0,scrollbars=1',
                    ],
                ],
                'readOnly' => 1,
                'softref' => 'typolink',
            ],
        ],
        'paramlink' => [
            'exclude' => 1,
            'label' => $once . 'paramlink',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '256',
                'eval' => 'trim',
                'wizards' => [
                    'link' => [
                        'type' => 'popup',
                        'title' => $once . 'linkTitle',
                        'icon' => 'link_popup.gif',
                        'module' => [
                            'name' => 'wizard_link',
                        ],
                        'JSopenParams' => 'height=800,width=600,status=0,menubar=0,scrollbars=1',
                    ],
                ],
                'readOnly' => 1,
                'softref' => 'typolink',
            ],
        ],
        'description' => [
            'exclude' => 1,
            'label' => $once . 'comment',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'comment' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'crdate' => [
            'exclude' => 0,
            'label' => $once . 'crdate',
            'config' => [
                'type' => 'input',
                'size' => 12,
                'eval' => 'datetime',
                'readOnly' => true,
                'default' => 0,
            ],
        ],
        'childcomment' => [
            'exclude' => 1,
            'label' => $once . 'childcomment',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_rdcomments_domain_model_comment',
                'foreign_field' => 'comment',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => true,
                    'newRecordLinkPosition' => 'none',
                    'showAllLocalizationLink' => false,
                    'showSynchronizationLink' => false,
                    'showNewRecordLink' => false,
                    'useSortable' => false,
                    'enabledControls' => [
                        'new' => false,
                        'dragdrop' => false,
                        'sort' => false,
                        'hide' => false,
                        'delete' => false,
                    ],
                ],
            ],
        ],
        'terms' => [
            'exclude' => 0,
            'label' => $once . 'terms',
            'config' => [
                'type' => 'check',
                'readOnly' => 1,
            ],
        ],
        'likes' => [
            'exclude' => 1,
            'label' => $once . 'likes',
            'config' => [
                'type' => 'text',
                'enableRichtext' => false,
                'eval' => 'trim',
                'default' => '',
            ]
        ],
        'pinned' => [
            'exclude' => 1,
            'label' => $once . 'pinned',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
    ],
];

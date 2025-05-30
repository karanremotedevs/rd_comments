<?php
defined('TYPO3') || die();

use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

(static function() {
    ExtensionUtility::configurePlugin(
        'RdComments',
        'RdComment',
        [
            \RemoteDevs\RdComments\Controller\CommentController::class => 'list, create, delete, like'
        ],
        // non-cacheable actions
        [
            \RemoteDevs\RdComments\Controller\CommentController::class => 'list, create, delete, like'
        ]
    );

    ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.commnentplugintab {
            header = LLL:EXT:rd_comments/Resources/Private/Language/locallang_db.xlf:commnentplugintab.header
                elements {
                    comments {
                        iconIdentifier = rd_comments-plugin-comment
                        title = LLL:EXT:rd_comments/Resources/Private/Language/locallang_db.xlf:tx_rdcomments_comment.name
                        description = LLL:EXT:rd_comments/Resources/Private/Language/locallang_db.xlf:tx_rdcomments_comment.description
                        tt_content_defValues {
                            CType = list
                            list_type = rdcomments_rdcomment
                        }
                    }
                }
                show = *
            }
       }'
    );
})();


<?php
defined('TYPO3') || die();

use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

(static function() {
    ExtensionUtility::configurePlugin(
        'Comments',
        'Comment',
        [
            \Rd\Comments\Controller\CommentController::class => 'list, create, delete, like'
        ],
        // non-cacheable actions
        [
            \Rd\Comments\Controller\CommentController::class => 'list, create, delete, like'
        ]
    );

    ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.commnentplugintab {
            header = LLL:EXT:comments/Resources/Private/Language/locallang_db.xlf:commnentplugintab.header

                elements {
                    comments {
                        iconIdentifier = comment-plugin-comment
                        title = LLL:EXT:comments/Resources/Private/Language/locallang_db.xlf:tx_comments_comment.name
                        description = LLL:EXT:comments/Resources/Private/Language/locallang_db.xlf:tx_comments_comment.description
                        tt_content_defValues {
                            CType = list
                            list_type = comments_comment
                        }
                    }
                }
                show = *
            }
       }'
    );
})();


<?php

declare(strict_types=1);

defined('TYPO3') || die();

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if ((GeneralUtility::makeInstance(Typo3Version::class))->getMajorVersion() == 11) {
    // @extensionScannerIgnoreLine
    ExtensionManagementUtility::addLLrefForTCAdescr('tx_comments_domain_model_comment', 'EXT:comments/Resources/Private/Language/locallang_csh_tx_comments_domain_model_comment.xlf');
    // @extensionScannerIgnoreLine
    ExtensionManagementUtility::allowTableOnStandardPages('tx_comments_domain_model_comment');
}

<?php
defined('TYPO3') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'rd_comments', 
    'Configuration/TypoScript',
    'RdComment'
);

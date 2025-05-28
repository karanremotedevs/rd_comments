<?php

namespace Rd\Comments\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use Rd\Comments\Domain\Repository\CommentRepository;

/**
 * This file is part of the "comment" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 Abhay Rathod <abhay.remotedevs@gmail.com>, RD
 */

/**
 *  Get last comment of news record
 */
class LastCommentViewHelper extends AbstractViewHelper
{
    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('newsuid', 'int', 'news uid', true);
    }

    /**
     * Last Comment
     *
     */
    public function render()
    {
        $newsUid = $this->arguments['newsuid'];
        $newsCommentData = [];
        if($newsUid) {
            $commentsRepository = GeneralUtility::makeInstance(CommentRepository::class);
            // Get last comment of news
            $newsCommentData = $commentsRepository->getLastCommentOfNews((int) $newsUid);
        }
        return $newsCommentData;

    }
}

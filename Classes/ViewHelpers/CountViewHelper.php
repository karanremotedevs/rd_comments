<?php

namespace Rd\Comments\ViewHelpers;

use Rd\Comments\Domain\Repository\CommentRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This file is part of the "comment" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 Abhay Rathod <abhay.remotedevs@gmail.com>, RD
 */

/**
 *  Get the counts of news comments
 */
class CountViewHelper extends AbstractViewHelper
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
    public function render(): int
    {
        $newsUid = (int) $this->arguments['newsuid'];
        $commentCount = 0;
        if ($newsUid) {
            $commentRepository = GeneralUtility::makeInstance(CommentRepository::class);
            // Get the counts of news comments
            $commentCount = $commentRepository->getCountOfComments($newsUid);
        }
        return $commentCount;
    }
}

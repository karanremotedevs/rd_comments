<?php

declare(strict_types=1);

namespace Rd\Comments\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * This file is part of the "comment" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 Abhay Rathod <abhay.remotedevs@gmail.com>, RD
 */

/**
 * The repository for Comment
 */
class CommentRepository extends Repository
{
    /**
     *
     * @param int $newsId
     */
    public function getCommentsByNews(int $newsId)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('newsuid', $newsId),
                $query->equals('comment', 0),
            )
        );
        $query->setOrderings([
            'pinned' =>QueryInterface::ORDER_DESCENDING,
            'crdate' => QueryInterface::ORDER_DESCENDING
        ]);
        return $query->execute();
    }

    /**
     *
     * @param int $newsUid
     */
    public function getLastCommentOfNews(int $newsUid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching($query->equals('newsuid', $newsUid));
        $query->setOrderings(['crdate' => QueryInterface::ORDER_DESCENDING]);
        return $query->setLimit(1)->execute();
    }

    /**
     *
     * @param int $newsId
     * @return int
     */
    public function getCountOfComments(int $newsId): int
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching($query->equals('newsuid', $newsId));
        return $query->execute()->count();
    }
}

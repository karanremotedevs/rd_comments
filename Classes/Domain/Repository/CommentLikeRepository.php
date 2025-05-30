<?php

namespace RemoteDevs\RdComments\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * This file is part of the "comment" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 Abhay Rathod <abhay.remotedevs@gmail.com>, RemoteDevs
 */

/**
 * CommentLikeRepository
 */
class CommentLikeRepository extends Repository
{
    /**
     * Summary of checkIfIpLiked
     * @param int $commentId
     * @param string $ip
     * @return bool
     */
    public function checkIfIpLiked(int $commentId, string $ip): bool
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('commentUid', $commentId),
                $query->equals('ipAddress', $ip)
            )
        );
        return $query->execute()->count() > 0;
    }
    
    /**
     * Summary of countLikesByCommentUid
     * @param int $commentUid
     * @return int
     */
    public function countLikesByCommentUid(int $commentUid): int
    {
        $query = $this->createQuery();
        $query->matching(
            $query->equals('commentUid', $commentUid)
        );
        return $query->execute()->count();
    }

    /**
     * Summary of getLikesCountForComments
     * @param array $commentUids
     * @return int[]
     */
    public function getLikesCountForComments(array $commentUids): array
    {
        if (empty($commentUids)) {
            return [];
        }

        $query = $this->createQuery();
        $query->matching(
            $query->in('commentUid', $commentUids)
        );
        $likes = $query->execute();

        $counts = [];
        foreach ($likes as $like) {
            $commentId = $like['commentUid'];
            if (!isset($counts[$commentId])) {
                $counts[$commentId] = 0;
            }
            $counts[$commentId]++;
        }
        return $counts; 
    }
}

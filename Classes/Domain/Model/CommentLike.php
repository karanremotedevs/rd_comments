<?php

namespace RemoteDevs\RdComments\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * This file is part of the "comment" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 Abhay Rathod <abhay.remotedevs@gmail.com>, RemoteDevs
 */

/**
 * CommentLike
 */ 
class CommentLike extends AbstractEntity
{
    protected int $commentUid = 0;

    protected string $ipAddress = '';

    protected int $crdate = 0;

    /**
     * @return int
     */
    public function getCommentUid(): int
    {
        return $this->commentUid;
    }

    /**
     * @param int $commentUid
     */
    public function setCommentUid(int $commentUid): void
    {
        $this->commentUid = $commentUid;
    }

    /**
     * @param string $ipAddress
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress(string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return int
     */
    public function getCrdate(): int
    {
        return $this->crdate;
    }

    /**
     * @param int $crdate
     */
    public function setCrdate(int $crdate): void
    {
        $this->crdate = $crdate;
    }
}
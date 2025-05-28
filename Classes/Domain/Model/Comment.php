<?php

declare(strict_types=1);

namespace Rd\Comments\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * This file is part of the "comment" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 Abhay Rathod <abhay.remotedevs@gmail.com>, RD
 */

/**
 * Comment
 */
class Comment extends AbstractEntity
{
    protected string $username = '';

    protected string $usermail = '';

    protected string $paramlink = '';

    protected int $pageid;

    protected int $crdate = 0;
    
    protected int $newsuid = 0;

    protected string $description = '';

    protected $childcomment = null;

    protected bool $terms = false;

    protected int $likes = 0;

    protected bool $pinned = false; 

    public function getLikes(): int
    {
        return $this->likes;
    }
    
    public function setLikes(int $likes): void
    {
        $this->likes = $likes;
    }
    
    protected bool $likedByCurrentUser = false;

    public function isLikedByCurrentUser(): bool
    {
        return $this->likedByCurrentUser;
    }

    public function setLikedByCurrentUser(bool $liked): void
    {
        $this->likedByCurrentUser = $liked;
    }

    public function getNewsuid(): int
    {
        return $this->newsuid;
    }

    public function setNewsuid($newsuid): void
    {
        $this->newsuid = $newsuid;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername($username): void
    {
        $this->username = $username;
    }

    public function getUsermail(): string
    {
        return $this->usermail;
    }

    public function setUsermail($usermail): void
    {
        $this->usermail = $usermail;
    }

    public function getParamlink(): string
    {
        return $this->paramlink;
    }

    public function setParamlink($paramlink): void
    {
        $this->paramlink = $paramlink;
    }

    public function getCrdate(): int
    {
        return $this->crdate;
    }

    public function setCrdate($crdate): void
    {
        $this->crdate = $crdate;
    }

    public function getPageid(): int
    {
        return $this->pageid;
    }

    public function setPageid($pageid): void
    {
        $this->pageid = $pageid;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPinned(): bool
    {
        return $this->pinned;
    }

    /**
     * @param bool $pinned
     */
    public function setPinned(bool $pinned): void
    {
        $this->pinned = $pinned;
    }

    /**
     * @param string $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function __construct()
    {
        //Do not remove the next line It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects(): void
    {
        $this->childcomment = new ObjectStorage();
    }

    /**
     * Adds a Comment
     *
     * @param Comment $childcomment
     * @return void
     */
    public function addChildcomment(self $childcomment): void
    {
        $this->childcomment->attach($childcomment);
    }

    /**
     * Returns the childcomment
     *
     * @return ObjectStorage<Comment> $childcomment
     */
    public function getChildcomment()
    {
        return $this->childcomment;
    }

    /**
     * Sets the childcomment
     *
     * @param ObjectStorage<Comment> $childcomment
     * @return void
     */
    public function setChildcomment(ObjectStorage $childcomment): void
    {
        $this->childcomment = $childcomment;
    }

    /**
     * @return bool
     */
    public function getTerms(): bool
    {
        return $this->terms;
    }

    /**
     * @param bool $terms
     * @return void
     */
    public function setTerms($terms): void
    {
        $this->terms = $terms;
    }

    /**
     * Set sys language
     *
     * @param int $sysLanguageUid
     */
    public function setSysLanguageUid($sysLanguageUid): void
    {
        $this->sysLanguageUid = $sysLanguageUid;
        $this->_languageUid = $sysLanguageUid;
    }

    /**
     * Get sys language
     *
     * @return int
     */
    public function getSysLanguageUid(): int
    {
        return $this->_languageUid;
    }

}
<?php

declare(strict_types=1);

namespace RemoteDevs\RdComments\Controller;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Core\Environment;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Install\Service\SessionService;
use RemoteDevs\RdComments\Domain\Model\Comment;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use GeorgRinger\News\Domain\Repository\NewsRepository;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Resource\Exception\InvalidFileException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use RemoteDevs\RdComments\Domain\Repository\CommentRepository;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use RemoteDevs\RdComments\Domain\Repository\CommentLikeRepository;
use RemoteDevs\RdComments\Domain\Model\CommentLike;


/**
 * This file is part of the "comment" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 Abhay Rathod <abhay.remotedevs@gmail.com>, RemoteDevs
 */

/**
 * CommentController
 */
class CommentController extends ActionController
{
    protected ?CommentRepository $CommentRepository = null;

    protected NewsRepository $newsRepository;

    protected PersistenceManager $persistenceManager;

    protected CommentLikeRepository $commentLikeRepository;

    protected int $newsUid;

    protected int $pageUid;

    /**
     * @var array
     */
    protected array $typo3VersionArray = [];

    /**
     * @param CommentLikeRepository $commentLikeRepository
     */
    public function injectCommentLikeRepository(CommentLikeRepository $commentLikeRepository): void
    {
        $this->commentLikeRepository = $commentLikeRepository;
    }

    /**
     * @param PersistenceManager $PersistenceManager
     */
    public function injectPersistenceManager(PersistenceManager $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @param CommentRepository $commentRepository
     */
    public function injectCommentRepository(CommentRepository $commentRepository)
    {
        $this->CommentRepository = $commentRepository;
    }

    /**
     * @param CommentRepository $CommentRepository
     * @param NewsRepository $newsRepository
     * @param PersistenceManager $persistenceManager
     */
    public function __construct(
        CommentRepository  $CommentRepository,
        PersistenceManager $persistenceManager,
        NewsRepository     $newsRepository
    ) {
        $this->CommentRepository = $CommentRepository;
        $this->persistenceManager = $persistenceManager;
        $this->newsRepository = $newsRepository;
    }

    /**
     * Initialize action
     */
    public function initializeAction(): void
    {
        $sessionService = GeneralUtility::makeInstance(SessionService::class);
        $sessionService->startSession();
        $this->typo3VersionArray = VersionNumberUtility::convertVersionStringToArray(VersionNumberUtility::getCurrentTypo3Version());
        $getData = $this->request->getQueryParams();
        $postData = $this->request->getParsedBody();
        $requestData = array_merge($getData, (array)$postData);
        $newsArr = $requestData['tx_news_pi1'] ?? [];
        $newsUid = '';
        if (is_null($newsArr)) {
            if (isset($_SESSION['params']) && $_SESSION['params']['originalSettings']['singleNews']) {
                $newsUid = $_SESSION['params']['originalSettings']['singleNews'];
            }
        } else {
            $newsUid = $newsArr['news'] ?? null;
        }
        $this->newsUid = (int)$newsUid;

        $this->pageUid = 0;
        if (isset($GLOBALS['TSFE']) && is_object($GLOBALS['TSFE']) && isset($GLOBALS['TSFE']->id)) {
            $this->pageUid = (int)$GLOBALS['TSFE']->id;
        }

        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

        if (empty($extbaseFrameworkConfiguration['persistence']['storagePid'])) {
            if (isset($_REQUEST['tx_rdcomments_domain_model_comment']['Storagepid'])) {
                $currentPid['persistence']['storagePid'] = $_REQUEST['tx_rdcomments_domain_model_comment']['Storagepid'];
            } else {
                if (isset($this->settings['storagePid']) && !empty($this->settings['storagePid'])) {
                    $currentPid['persistence']['storagePid'] = $this->settings['storagePid'];
                } else {
                    $currentPid['persistence']['storagePid'] = $this->pageUid;
                }
            }
            $this->configurationManager->setConfiguration(array_merge($extbaseFrameworkConfiguration, $currentPid));
        }
    }

    /**
     * list action
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );

        $pid = empty($extbaseFrameworkConfiguration['persistence']['storagePid'])
            ? $this->pageUid
            : $extbaseFrameworkConfiguration['persistence']['storagePid'];

        $clientIp = $this->getClientIp();

        if ($this->newsUid) {
            $comments = $this->CommentRepository->getCommentsByNews($this->newsUid);
            $commentsArray = $comments instanceof \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
                ? $comments->toArray()
                : iterator_to_array($comments);

            foreach ($commentsArray as $comment) {
                $this->markLikedStatusRecursively($comment, $clientIp);
            }

            $this->view->assignMultiple([
                'comments' => $commentsArray,
                'newsID' => $this->newsUid,
                'pageid' => $this->pageUid,
                'pid' => $pid,
                'settings' => $this->settings,
            ]);
        }
        return $this->htmlResponse();
    }

    /**
     * Recursively mark liked status for comment and its replies
     *
     * @param Comment $comment
     * @param string $clientIp
     */
    protected function markLikedStatusRecursively(Comment $comment, string $clientIp): void
    {
        $commentId = $comment->getUid();
        $liked = $this->commentLikeRepository->checkIfIpLiked($commentId, $clientIp);
        $comment->setLikedByCurrentUser($liked);

        foreach ($comment->getChildcomment() as $childComment) {
            $this->markLikedStatusRecursively($childComment, $clientIp);
        }
    }

    /**
     * create action
     *
     * @param \RemoteDevs\Comments\Domain\Model\Comment $newComment
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createAction(Comment $newComment): ResponseInterface
    {
        $requestData = $this->request->getArguments();

        $newComment->setCrdate(time());
        $language = GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('language', 'id');
        $newComment->setSysLanguageUid($language);

        $parentId = (int)($requestData['parentId']);

        if ($parentId > 0) {
            $childComment = $this->CommentRepository->findByUid($parentId);
            $childComment->addChildcomment($newComment);
            $this->CommentRepository->update($childComment);
        }

        $this->CommentRepository->add($newComment);
        $this->persistenceManager->persistAll();
        $news = $this->newsRepository->findByUid($newComment->getNewsuid());
        if ($news) {
            $paramLink = $this->buildUriByUid(
                $this->pageUid,
                $news,
                ['commentid' => $newComment->getUid()]
            );
            $newComment->setParamlink($paramLink);
            $this->CommentRepository->update($newComment);
            $this->persistenceManager->persistAll();
        }

        $json[$newComment->getUid()] = [
            'parentId' => $parentId,
            'comment' => 'comment',
        ];
        return new JsonResponse($json);
    }

    /**
     * @param int $uid
     * @param string $news
     * @param array $arguments
     * @return string
     */
    private function buildUriByUid(int $uid, $news, array $arguments = []): string
    {
        $commentId = $arguments['commentid'];

        $excludeFromQueryString = [
            'tx_rdcomment_newscomments[action]',
            'tx_rdcomment_newscomments[controller]',
            'tx_rdcomment_newscomments',
            'type'
        ];

        $this->uriBuilder
            ->reset()
            ->setTargetPageUid($uid)
            ->setAddQueryString(true)
            ->setArgumentsToBeExcludedFromQueryString($excludeFromQueryString)
            ->setSection('comments-' . $commentId);

        if (array_key_exists('formail', $arguments)) {
            $this->uriBuilder->setArguments(['frommail' => 1]);
        }

        $uri = $this->uriBuilder->uriFor('detail', ['news' => $news], 'News', 'News', 'Pi1');

        return $this->addBaseUriIfNecessary($uri);
    }

    /**
     * @param string $uri
     * @return string
     */
    private function getClientIp(): ?string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }

    /**
     * @param int $commentId
     * @param string $ip
     * @return bool
     */
    protected function checkIfIpLiked(int $commentId, string $ip): bool
    {
        $query = $this->commentLikeRepository->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('commentUid', $commentId),
                $query->equals('ipAddress', $ip)
            )
        );
        return $query->execute()->count() > 0;
    }

    /**
     * @param int $commentId
     * @param string $ip
     */
    public function storeIpLike(int $commentId, string $ip): void
    {
        if ($this->checkIfIpLiked($commentId, $ip)) {
            return;
        }

        $like = new CommentLike();
        $like->setCommentUid($commentId);
        $like->setIpAddress($ip);
        $like->setCrdate(time());
        $this->commentLikeRepository->add($like);
        $this->persistenceManager->persistAll();
    }

    /**
     * @param int $commentId
     * @param string $ip
     */
    public function removeIpLike(int $commentId, string $ip): void
    {
        $query = $this->commentLikeRepository->createQuery();
        $query->matching(
            $query->logicalAnd(
                $query->equals('commentUid', $commentId),
                $query->equals('ipAddress', $ip)
            )
        );
        $results = $query->execute();

        foreach ($results as $like) {
            $this->commentLikeRepository->remove($like);
        }

        $this->persistenceManager->persistAll();
    }

    /**
     * @return ResponseInterface
     */
    public function likeAction(): ResponseInterface
    {
        $commentId = (int)$this->request->getArgument('commentId');
        $action = (string)$this->request->getArgument('userAction');

        $ip = $this->getClientIp();

        if (!$commentId || !in_array($action, ['like', 'unlike'], true)) {
            return new JsonResponse(['success' => false, 'error' => 'Invalid parameters']);
        }

        $comment = $this->CommentRepository->findByUid($commentId);
        if (!$comment) {
            return new JsonResponse(['success' => false, 'error' => 'Comment not found']);
        }

        $likes = $comment->getLikes() ?? 0;

        if ($action === 'like') {
            if (!$this->checkIfIpLiked($commentId, $ip)) {
                $likes++;
                $this->storeIpLike($commentId, $ip);
            } else {
                return new JsonResponse(['success' => false, 'error' => 'Already liked']);
            }
        } else {
            if ($this->checkIfIpLiked($commentId, $ip)) {
                $likes = max(0, $likes - 1);
                $this->removeIpLike($commentId, $ip);
            } else {
                return new JsonResponse(['success' => false, 'error' => 'Not liked yet']);
            }
        }

        $comment->setLikes($likes);
        $this->CommentRepository->update($comment);
        $this->persistenceManager->persistAll();

        return new JsonResponse([
            'success'   => true,
            'likes'     => $likes,
            'commentId' => $commentId,
            'action'    => $action,
        ]);
    }

    /*
     * @param string $uri
     * @return string
     *  
     */
    public function backendListAction(): ResponseInterface
    {
        $queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
            ->getQueryBuilderForTable('tx_rdcomments_domain_model_comment');

        $commentsRaw = $queryBuilder
            ->select('*')
            ->from('tx_rdcomments_domain_model_comment')
            ->where(
                $queryBuilder->expr()->eq('deleted', 0),
                $queryBuilder->expr()->eq('hidden', 0)
            )
            ->orderBy('pinned', 'DESC')
            ->addOrderBy('crdate', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative();

        $allComments = [];
        $repliesMap = [];

        foreach ($commentsRaw as $commentRaw) {
            $commentUid = (int)$commentRaw['uid'];
            $parentCommentId = (int)$commentRaw['comment'];
            $newsId = (int)$commentRaw['newsuid'];

            $allComments[$commentUid] = [
                'uid' => $commentUid,
                'newsuid' => $newsId,
                'username' => $commentRaw['username'] ?: 'Anonymous',
                'usermail' => $commentRaw['usermail'],
                'crdate' => $commentRaw['crdate'] ? date('Y-m-d H:i', $commentRaw['crdate']) : '',
                'description' => $commentRaw['description'] ?: '',
                'pinned' => (bool)$commentRaw['pinned'],
                'likes' => (int)$commentRaw['likes'],
                'replies' => [],
                'parent' => $parentCommentId,
                'is_top_level' => empty($parentCommentId),
            ];

            if (!empty($parentCommentId)) {
                $repliesMap[$parentCommentId][] = $commentUid;
            }
        }

        $buildReplies = function ($comment) use (&$allComments, &$repliesMap, &$buildReplies) {
            $uid = $comment['uid'];
            if (!empty($repliesMap[$uid])) {
                foreach ($repliesMap[$uid] as $replyUid) {
                    if (isset($allComments[$replyUid])) {
                        $child = $allComments[$replyUid];
                        $comment['replies'][] = $buildReplies($child);
                    }
                }
            }
            return $comment;
        };

        $commentsByNews = [];

        foreach ($allComments as $comment) {
            if ($comment['is_top_level']) {
                $newsId = $comment['newsuid'];
                if (!isset($commentsByNews[$newsId])) {
                    $commentsByNews[$newsId] = [
                        'newsId' => $newsId,
                        'newsTitle' => '',
                        'comments' => []
                    ];
                }
                $commentsByNews[$newsId]['comments'][] = $buildReplies($comment);
            }
        }

        foreach ($commentsByNews as &$newsItem) {
            usort($newsItem['comments'], fn($a, $b) => ($b['pinned'] <=> $a['pinned']) ?: strtotime($b['crdate']) <=> strtotime($a['crdate']));
            $news = $this->newsRepository->findByUid($newsItem['newsId']);
            $newsItem['newsTitle'] = $news ? $news->getTitle() : 'News #' . $newsItem['newsId'] . ' (Not Found)';
        }

        $formattedNewsList = array_map(fn($newsItem) => [
            'uid' => $newsItem['newsId'],
            'title' => $newsItem['newsTitle']
        ], $commentsByNews);

        $this->view->assignMultiple([
            'newsList' => $formattedNewsList,
            'commentsByNews' => $commentsByNews,
        ]);

        return $this->htmlResponse();
    }

    /**
     * @param string $uri
     * @return string
     */
    protected function addBaseUriIfNecessary($uri): string
    {
        if (PathUtility::isAbsolutePath($uri) || preg_match('#^(\w+:)?//#', $uri)) {
            return (string)$uri;
        }

        $baseUri = '';
        if (isset($GLOBALS['TSFE']) && is_object($GLOBALS['TSFE'])) {
            $baseUri = $GLOBALS['TSFE']->getSite()->getBase()->__toString();
        } elseif (Environment::isCli()) {
            $baseUri = '/';
        } else {
            $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
            if ($request instanceof \Psr\Http\Message\ServerRequestInterface) {
                $baseUri = $request->getUri()->__toString();
            }
        }

        return rtrim((string)$baseUri, '/') . '/' . ltrim((string)$uri, '/');
    }

    /**
     * Delete action
     *
     * @param int $commentUid
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteAction(int $commentUid): ResponseInterface
    {
        $comment = $this->CommentRepository->findByUid($commentUid);
        if ($comment instanceof Comment) {
            $childComments = $comment->getChildcomment();
            if ($childComments) {
                foreach ($childComments as $childComment) {
                    $this->CommentRepository->remove($childComment);
                }
            }
            $this->CommentRepository->remove($comment);
            $this->persistenceManager->persistAll();
            $this->addFlashMessage(
                LocalizationUtility::translate('comment_deleted_successfully', 'Comments') ?? 'Comment deleted successfully.',
                'Success',
                ContextualFeedbackSeverity::OK
            );
        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('comment_not_found', 'Comments') ?? 'Comment not found.',
                'Error',
                ContextualFeedbackSeverity::ERROR
            );
        }

        return $this->redirect('backendList');
    }

    /**
     * Pin action
     *
     * @param int $commentUid
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function pinAction(int $commentUid): ResponseInterface
    {
        $comment = $this->CommentRepository->findByUid($commentUid);
        if ($comment instanceof Comment) {
            $comment->setPinned(!$comment->getPinned());
            $this->CommentRepository->update($comment);
            $this->persistenceManager->persistAll();
            $message = $comment->getPinned() ? 'comment_pinned_successfully' : 'comment_unpinned_successfully';
            $this->addFlashMessage(
                LocalizationUtility::translate($message, 'Comments') ?? $message,
                'Success',
                ContextualFeedbackSeverity::OK
            );
        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('comment_not_found', 'Comments') ?? 'Comment not found',
                'Error',
                ContextualFeedbackSeverity::ERROR
            );
        }

        return $this->redirect('backendList');
    }
}

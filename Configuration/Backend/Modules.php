<?php

declare(strict_types=1);

use RemoteDevs\RdComments\Controller\CommentController;

return [
    'comments' => [
        'parent' => 'web',
        'access' => 'systemMaintainer',
        'path' => '/module/comments',
        'iconIdentifier' => 'comment-plugin-comment',
        'labels' => 'LLL:EXT:rd_comments/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'RdComments',
        'navigationComponentId' => 'TYPO3.Backend',
        'routes' => [
            'backendList' => [
                'target' => CommentController::class . '::backendListAction',
            ],
            'delete' => [
                'target' => CommentController::class . '::deleteAction',
            ],
            'pin' => [
                'target' => CommentController::class . '::pinAction',
            ],
        ],
        'controllerActions' => [
            CommentController::class => [
                'backendList',
                'delete',
                'pin',
            ],
        ],
    ],
];
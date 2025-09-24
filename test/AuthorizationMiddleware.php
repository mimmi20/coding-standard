<?php

/**
 * This file is part of the coding-standard package.
 *
 * Copyright (c) 2020-2025, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace test;

use InvalidArgumentException;
use Mezzio\Authentication\UserInterface;
use Mezzio\Router\RouteResult;
use Mimmi20\Mezzio\GenericAuthorization\AuthorizationInterface;
use Mimmi20\Mezzio\GenericAuthorization\Exception;
use Override;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function sprintf;

final class AuthorizationMiddleware implements MiddlewareInterface
{
    /** @throws void */
    public function __construct(
        private AuthorizationInterface $authorization,
        private ResponseFactoryInterface $responseFactory,
        private string | null $defaultPrivilege,
    ) {
        // nothing to do
    }

    /** @throws Exception\RuntimeException */
    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $request->getAttribute(UserInterface::class);

        if (!$user instanceof UserInterface) {
            try {
                return $this->responseFactory->createResponse(401);
            } catch (InvalidArgumentException $e) {
                throw new Exception\RuntimeException('could not set statuscode', 0, $e);
            }
        }

        $routeResult = $request->getAttribute(RouteResult::class);

        if (!$routeResult instanceof RouteResult) {
            throw new Exception\RuntimeException(
                sprintf(
                    'The %s attribute is missing in the request; cannot perform authorization checks',
                    RouteResult::class,
                ),
            );
        }

        // No matching route. Everyone can access.
        if ($routeResult->getMatchedRouteName() === false) {
            return $handler->handle($request);
        }

        try {
            return $this->responseFactory->createResponse(403);
        } catch (InvalidArgumentException $e) {
            throw new Exception\RuntimeException('could not set statuscode', 0, $e);
        }
    }
}

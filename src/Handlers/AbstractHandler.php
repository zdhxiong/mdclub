<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Abstracts\ContainerAbstracts;
use Psr\Http\Message\ServerRequestInterface;

/**
 */
abstract class AbstractHandler extends ContainerAbstracts
{
    /**
     * Known handled content types
     *
     * @var array
     */
    protected static $knownContentTypes = [
        'application/json',
        'application/xml',
        'text/xml',
        'text/html',
    ];

    /**
     * Determine which content type we know about is wanted using Accept header
     *
     * @param  ServerRequestInterface $request
     * @return string
     */
    protected function determineContentType(ServerRequestInterface $request): string
    {
        $acceptHeader = $request->getHeaderLine('Accept');
        $selectedContentTypes = array_intersect(explode(',', $acceptHeader), self::$knownContentTypes);

        if (count($selectedContentTypes)) {
            return current($selectedContentTypes);
        }

        // handle +json and +xml specially
        if (preg_match('/\+(json|xml)/', $acceptHeader, $matches)) {
            $mediaType = 'application/' . $matches[1];

            if (in_array($mediaType, self::$knownContentTypes, true)) {
                return $mediaType;
            }
        }

        return 'text/html';
    }
}

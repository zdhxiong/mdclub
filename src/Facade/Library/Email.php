<?php

declare(strict_types=1);

namespace MDClub\Facade\Library;

use MDClub\Initializer\Facade;

/**
 * Email Facade
 *
 * @method static void   send($to, string $subject, string $body)
 * @method static void   sendByTemplate(string $template, $to, string $subject, array $data)
 * @method static bool   checkCode(string $email, string $code)
 * @method static string generateCode(string $email)
 */
class Email extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return \MDClub\Library\Email::class;
    }
}

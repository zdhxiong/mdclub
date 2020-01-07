<?php

declare(strict_types=1);

namespace MDClub\Validator;

/**
 * 发送邮件的验证
 */
class Email extends Abstracts
{
    protected $attributes = [
        'to' => '邮箱地址',
        'subject' => '邮件标题',
        'body' => '邮件正文'
    ];

    /**
     * 发送邮件前进行验证
     *
     * @param  array $data [to, subject, body]
     * @return array
     */
    public function send(array $data): array
    {
        return $this->data($data)
            ->field('to')->exist()->arrayLength(1, 100)->arrayUnique()->eachEmail()
            ->field('subject')->exist()->string()->notEmpty()
            ->field('body')->exist()->string()->notEmpty()
            ->validate();
    }
}

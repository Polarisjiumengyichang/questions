<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Service;

use App\Amqp\Producer\MailProducer;
use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use Hyperf\Amqp\Producer;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\TranslatorInterface;

class IndexService
{
    public function info(int $id)
    {
        if ($id <= 0) {
            // $translator = ApplicationContext::getContainer()->get(TranslatorInterface::class);
            // $translator->setLocale('ja');
            // throw new BusinessException(trans('params.id_invalid'),500);
            // throw new BusinessException(ErrorCode::getMessage(ErrorCode::PARAMS_ID_INVALID));
            throw new BusinessException(ErrorCode::PARAMS_ID_INVALID);
        }

        return ['info' => 'data info'];
    }

    public function email()
    {
        $startTime = microtime(true);
        $mailInfo = [
            'to' => '604698796@qq.com',
            'subject' => '邮件测试标题111',
            'body' => '<b style="color: #f00;">邮件测试内容222</b>',
        ];
        $message = new MailProducer($mailInfo);
        $producer = ApplicationContext::getContainer()->get(Producer::class);
        $producer->produce($message);

        $runTime = '耗时: ' . (microtime(true) - $startTime) . ' s';

        return ['runtime' => $runTime];
    }
}

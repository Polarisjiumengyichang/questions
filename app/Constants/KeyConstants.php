<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants as AnnotationConstants;


#[AnnotationConstants]
class KeyConstants extends AbstractConstants
{
    public const USER_INFO = 'user:%d';
    public const QUESTION_HOT_RANK = 'question_hot';

    public const QUESTION_INFO = 'question:%d';
    public const ANSWER_SUPPORT = 'answer:support:%d';
}

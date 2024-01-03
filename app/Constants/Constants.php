<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants as AnnotationConstants;

#[AnnotationConstants]
class Constants extends AbstractConstants
{
    public const AUTHORIZATION = 'Authorization';
}
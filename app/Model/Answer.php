<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class Answer extends Model
{
    public const CREATED_AT = 'create_time';

    public const UPDATED_AT = null;

    protected ?string $dateFormat = 'U';
    
    protected ?string $table = 'answer';

    protected array $fillable = ['uid', 'question_id', 'pid', 'content', 'supports'];

    protected array $casts = ['id' => 'integer', 'uid' => 'integer', 'question_id' => 'integer', 'create_time' => 'integer',
        'pid' => 'integer', 'supports' => 'integer',
    ];
}

<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $uid 
 * @property int $answers 
 * @property int $supports 
 */
class UserDynamic extends Model
{
 
    protected ?string $table = 'user_dynamic';

    public bool $timestamps = false;

    protected array $fillable = ['uid', 'answers', 'supports'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['uid' => 'integer', 'answers' => 'integer', 'supports' => 'integer'];
}

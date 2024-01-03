<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 
 * @property string $email 
 * @property string $code 
 * @property int $create_time 
 */
class EmailCode extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'email_code';

    public const CREATED_AT = 'create_time';
    public const UPDATED_AT = null;

    protected ?string $dateFormat = 'U';

    protected array $fillable = ['email', 'code'];

    protected array $casts = ['id' => 'integer', 'create_time' => 'integer'];
}

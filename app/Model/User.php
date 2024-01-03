<?php

declare (strict_types = 1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;
use Hyperf\ModelCache\Cacheable;
use Hyperf\ModelCache\CacheableInterface;

/**
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $pic
 * @property string $nickname
 * @property int $create_time
 */
// class User extends Model
class User extends Model implements CacheableInterface
{
    use Cacheable;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'users';

    public const CREATED_AT = 'create_time';
    public const UPDATED_AT = null;

    protected ?string $dateFormat = 'U';

    protected array $fillable = ['email', 'password', 'pic', 'nickname'];

    protected array $casts = ['id' => 'integer', 'create_time' => 'integer'];

    public function getUserInfo($id)
    {

        $userInfo = User::query()->where('id', $id)->select('id', 'email', 'pic', 'create_time', 'nickname')->get()->toArray();

        return $userInfo;
    }

       /**
     * @param array $ids
     * @return mixed[]
     */
    public function getUserInfos(array $ids)
    {
        return User::query()->whereIn('id', $ids)->select('id', 'email', 'pic', 'create_time', 'nickname')->get()->toArray();
    }
}

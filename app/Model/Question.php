<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\Scout\Searchable;
use App\Components\FileAdapter;

/**
 * @property int $id 
 * @property int $uid 
 * @property string $title 
 * @property string $content_path 
 * @property string $content_hash 
 * @property int $create_time 
 * @property int $update_time 
 */
class Question extends Model
{
    use Searchable;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'question';

    public const CREATED_AT = 'create_time';

    public const UPDATED_AT = 'update_time';

    protected ?string $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['uid', 'title', 'content_path', 'content_hash', 'create_time', 'update_time'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'uid' => 'integer', 'create_time' => 'integer', 'update_time' => 'integer'];

    /*
     * 配置可搜索的数据
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $contentFlag = false;
        $searchRaw = Question::search()->where('id', $array['id'])->raw();
        if (! empty($searchRaw)) {
            $searchHit = $searchRaw['hits']['hits'] ?? [];
            // 这里要避免不必要的读
            if (! $searchHit || (! empty($searchHit) && $searchHit[0]['_source']['content_hash'] != $array['content_hash'])) {
                $contentFlag = true;
            }
        } else {
            $contentFlag = true;
        }
        if ($contentFlag && ! empty($array['content_path'])) {
            $adapter = make(FileAdapter::class);
            $array['content'] = $adapter->read($array['content_path']);
        }
        unset($array['content_path']);
        return $array;
    }
}
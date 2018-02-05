<?php

namespace App\Models;

use Yadakhov\InsertOnDuplicateKey;
use Illuminate\Database\Eloquent\Model;

class DnzbFailure extends Model
{
    use InsertOnDuplicateKey;
    /**
     * @var string
     */
    protected $table = 'dnzb_failures';

    /**
     * @var bool
     */
    protected $dateFormat = false;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $guarded = [];

    public function release()
    {
        return $this->belongsTo(Release::class, 'release_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Read failed downloads count for requested release_id.
     *
     *
     * @param $relId
     *
     * @return bool|mixed
     */
    public static function getFailedCount($relId)
    {
        $result = self::query()->where('release_id', $relId)->value('failed');
        if (! empty($result)) {
            return $result;
        }

        return false;
    }

    /**
     * @return int
     */
    public static function getCount(): int
    {
        return self::query()->count('release_id');
    }
}

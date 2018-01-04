<?php

namespace nntmux;

use Illuminate\Support\Carbon;
use App\Models\Video;

/**
 * Class Videos -- functions for site interaction.
 */
class Videos
{
    /**
     * Videos constructor.
     */
    public function __construct()
    {
    }

    /**
     * Get info from tables for the provided ID.
     *
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function getByVideoID($id)
    {
        return Video::query()
            ->where('videos.id', $id)
            ->join('tv_info', 'videos.id', '=', 'tv_info.videos_id')
            ->first(['videos.*', 'tv_info.summary', 'tv_info.publisher', 'tv_info.image']);
    }

    /**
     * Retrieves a range of all shows for the show-edit admin list.
     *
     *
     * @param $start
     * @param $num
     * @param string $showname
     * @return array
     */
    public function getRange($start, $num, $showname = ''): array
    {
        $sql = Video::query()
            ->select(['videos.*', 'tv_info.summary', 'tv_info.publisher', 'tv_info.image'])
            ->join('tv_info', 'videos.id', '=', 'tv_info.videos_id');

        if ($showname !== '') {
            $sql->where('videos.title', 'like', '%'.$showname.'%');
        }

        if ($start !== false) {
            $sql->limit($num)->offset($start);
        }

        return $sql->get()->toArray();
    }

    /**
     * Returns a count of all shows -- usually used by pager.
     *
     *
     * @param string $showname
     * @return int
     */
    public function getCount($showname = ''): int
    {
        $res = Video::query()->join('tv_info', 'videos.id', '=', 'tv_info.videos_id');

        if ($showname !== '') {
            $res->where('videos.title', 'like', '%'.$showname.'%');
        }

        return $res->count('videos.id');
    }

    /**
     * Retrieves and returns a list of shows with eligible releases.
     *
     * @param        $uid
     * @param string $letter
     * @param string $showname
     *
     * @return array
     */
    public function getSeriesList($uid, $letter = '', $showname = ''): array
    {
        if ($letter !== '') {
            if ($letter === '0-9') {
                $letter = '[0-9]';
            }
        }

        $qry = Video::query()
            ->select(['videos.*', 'tve.firstaired as prevdate', 'tve.title as previnfo', 'tvi.publisher', 'us.id as userseriesid'])
            ->join('tv_info as tvi', 'videos.id', '=', 'tvi.videos_id')
            ->join('tv_episodes as tve', 'videos.id', '=', 'tve.videos_id')
            ->leftJoin('user_series as us', 'videos.id', '=', 'us.videos_id')
            ->whereBetween('r.categories_id', [Category::TV_ROOT, Category::TV_OTHER])
            ->where('tve.firstaired', '<', Carbon::now())
            ->where('us.users_id', '=', $uid)
            ->leftJoin('releases as r', 'r.videos_id', '=', 'videos.id')
            ->orderBy('videos.title')
            ->orderByDesc('tve.firstaired')
            ->groupBy(['videos.id']);

        if ($letter !== '') {
            $qry->whereRaw('videos.title REGEXP ?', ['^'.$letter]);
        }

        if ($showname !== '') {
            $qry->where('videos.title', 'like', '%'.$showname.'%');
        }

        return $qry->get()->toArray();
    }
}

<?php

namespace App\Repository;

use App\Models\Diary;
use Illuminate\Pagination\LengthAwarePaginator;

class DiaryRepository extends BaseRepository
{
    public function __construct(Diary $diary)
    {
        parent::__construct($diary);
    }

    /**
     * 主キーの昇順or降順でページネーション取得
     */
    public function getPaginatedOrderedById(bool $is_desc = false): LengthAwarePaginator
    {
        $order = $is_desc ? 'desc' : 'asc';

        return $this->model->query()
            ->orderBy('diary_id', $order)
            ->paginate(config('app.pagination.low'));
    }
}

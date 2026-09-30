<?php

namespace App\Repository;

use App\Models\Diary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class DiaryRepository
{
    protected $model;

    public function __construct(Diary $diary)
    {
        $this->model = $diary;
    }

    /**
     * 作成
     */
    public function create(array $params): Model
    {
        return $this->model->create($params);
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

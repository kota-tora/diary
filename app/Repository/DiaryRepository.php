<?php

namespace App\Repository;

use App\Models\Diary;
use Illuminate\Database\Eloquent\Model;

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
}

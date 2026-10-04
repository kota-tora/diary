<?php

namespace App\Repository;

use Illuminate\Database\Eloquent\Model;

/**
 * 各リポジトリのBaseクラス
 */
class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * 作成
     */
    public function create(array $params): Model
    {
        return $this->model->create($params);
    }

    /**
     * 更新
     */
    public function update(Model $model, array $params): bool
    {
        return $model->update($params);
    }

    /**
     * 削除処理
     */
    public function delete(Model $model): ?bool
    {
        return $model->delete();
    }
}

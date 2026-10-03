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
     * @param array $params
     */
    public function create(array $params): Model
    {
        return $this->model->create($params);
    }

    /**
     * 更新
     * @param Model $model
     * @param array $params
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

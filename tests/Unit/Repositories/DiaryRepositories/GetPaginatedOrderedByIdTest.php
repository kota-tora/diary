<?php

use App\Models\Diary;
use App\Repository\DiaryRepository;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * テスト対象のgetPaginatedOrderedByIdを実行して返却
 */
function getDiaryActualData(bool $is_desc): LengthAwarePaginator
{
    $count = config('app.pagination.low');
    Diary::factory()->count($count)->create();
    $diary_repository = app()->make(DiaryRepository::class);

    // テスト対象を実行し昇順データ取得
    return $diary_repository->getPaginatedOrderedById($is_desc);
}

/**
 * 昇順、降順を引数で取得してテスト実施
 */
function assertDiaryOrderTest(bool $is_desc): void
{
    $order = $is_desc ? 'desc' : 'asc';
    $count = config('app.pagination.low');
    // テスト対象を実行し昇順データ取得
    $paginator = getDiaryActualData($is_desc);
    // 主キーの配列化
    $actual_ids = $paginator->pluck('diary_id')->toArray();
    // 期待結果の配列
    $expected_ids = Diary::query()->orderBy('diary_id', $order)->take($count)->pluck('diary_id')->toArray();
    // 同じ配列かを検証
    expect($actual_ids)->toBe($expected_ids);
}

it('paginateデータがdiary_idで昇順になっている', function (): void {
    // 昇順指定してテスト実施
    assertDiaryOrderTest(false);
});

it('paginateデータがdiary_idで降順になっている', function (): void {
    // 降順指定してテスト実施
    assertDiaryOrderTest(true);
});

it('1ページの件数が正しいか', function (): void {
    $diary_repository = app()->make(DiaryRepository::class);
    // テスト対象を実行し昇順データ取得
    $paginator = $diary_repository->getPaginatedOrderedById();

    // 1ページの件数がconfigと一致するか
    expect($paginator->perPage())->toBe(config('app.pagination.low'));
});

<?php

use App\Models\Diary;
use App\Repository\DiaryRepository;

it('存在するidを指定して更新できる', function (): void {
    $diary = Diary::factory()->create(['content' => '更新前', 'img_name' => 'before_update.jpg']);
    $repository = app()->make(DiaryRepository::class);
    // 更新データ
    $expect = ['content' => '更新後', 'img_name' => 'after_update.jpg'];
    // テスト対象メソッド実施
    $repository->update($diary, $expect);
    $actual = Diary::find($diary->diary_id);
    // 更新したデータが期待値と一致するか
    expect($actual->toArray())->toMatchArray($expect);
});

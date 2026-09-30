<?php

use App\Models\Diary;

it('ルーティングが正しく200を返す', function (): void {
    $response = $this->get(route('diary.index'));
    $response->assertOk();
});

it('正しいビューが使われている', function (): void {
    $response = $this->get(route('diary.index'));
    $response->assertViewIs('diaries.index');
});

it('データがある場合、内容が画面に表示される', function (): void {
    Diary::factory()->count(1)->create(['content' => 'テスト日記']);
    $response = $this->get(route('diary.index'));

    $response->assertSee('テスト日記');
});

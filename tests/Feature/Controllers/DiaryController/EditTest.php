<?php

use App\Models\Diary;

it('ルーティングが正しく200を返す', function (): void {
    $diary = Diary::factory()->create();
    $response = $this->get(route('diary.edit', $diary));
    $response->assertOk();
});

it('正しいビューが使われ、編集画面になっている', function (): void {
    $diary = Diary::factory()->create();
    $response = $this->get(route('diary.edit', $diary));
    $response->assertViewIs('diaries.create');
    $response->assertViewHas('is_update', true);
});

it('対象の日記が渡されている', function (): void {
    $diary = Diary::factory()->create();
    $response = $this->get(route('diary.edit', $diary));
    // viewに渡された日記が正しいか
    $response->assertViewHas('diary', $diary);
});

it('存在しないIDは404', function (): void {
    $response = $this->get(route('diary.edit', 100));
    $response->assertNotFound();
});

it('論理削除済みは404', function (): void {
    $diary = Diary::factory()->create();
    $diary->delete();
    $response = $this->get(route('diary.edit', $diary));
    $response->assertNotFound();
});

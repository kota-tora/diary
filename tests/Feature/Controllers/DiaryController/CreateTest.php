<?php

it('ルーティングが正しく200を返す', function (): void {
    $response = $this->get(route('diary.create'));
    $response->assertOk();
});

it('正しいビューが使われ、追加画面になっている', function (): void {
    $response = $this->get(route('diary.create'));
    $response->assertViewIs('diaries.create');
    $response->assertViewHas('is_update', false);
});

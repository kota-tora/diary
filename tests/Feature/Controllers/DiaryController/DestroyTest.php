<?php

use App\Models\Diary;
use App\Repository\DiaryRepository;
use Illuminate\Support\Facades\Log;

it('削除できてDBに反映される', function (): void {
    $diary = Diary::factory()->create();
    // 削除リクエスト実施
    $response = $this->delete(route('diary.destroy', $diary));
    // redirectされる
    $response->assertRedirect(route('diary.index'));
    // 成功セッションがある
    $response->assertSessionHas('success', '日記を削除しました。');
    // 対象データが論理削除されている
    $this->assertSoftDeleted($diary);
});

it('存在しないIDを指定した場合は404になる', function (): void {
    // 存在しないIDで削除リクエスト実施
    $response = $this->delete(route('diary.destroy', 100));
    $response->assertNotFound();
});

it('削除に失敗した場合、エラーメッセージとログが出る', function (): void {
    Log::spy();
    $diary = Diary::factory()->create();

    // モックして例外を発生
    $this->mock(DiaryRepository::class, function ($mock) {
        $mock->shouldReceive('delete')
            ->once()
            ->andThrow(new Exception('DBエラー発生'));
    });

    // 削除リクエスト実施
    $response = $this->delete(route('diary.destroy', $diary));
    // redirectされる
    $response->assertRedirect(route('diary.index'));
    // 失敗セッションがある
    $response->assertSessionHas('error', '日記を削除できませんでした。');

    // 対象データが論理削除されていない
    $this->assertNotSoftDeleted($diary);
    // ログが作られている
    Log::shouldHaveReceived('error')->once();
});

<?php

use App\Models\Diary;
use App\Services\DiaryService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // 仮想ディスクに差し替え
    Storage::fake('public');
});

it('画像ありで更新：リダイレクトとsuccessセッション、DB、新しい画像がある、古い画像が消えている', function (): void {
    // 更新前のデータ作成
    $before_diary = Diary::factory()->create();
    Storage::disk('public')->put('images/diaries/'.$before_diary->img_name, 'dummy');
    // 更新用データ
    $update_img = UploadedFile::fake()->image('update.jpg');
    $update_content = [
        'content' => 'test投稿',
        'image' => $update_img,
    ];

    // テスト対象メソッド実行
    $response = $this->put(route('diary.update', $before_diary), $update_content);

    // 成功時のセッションを検証
    $response->assertSessionHas('success', '日記を更新しました。');
    // 一覧にリダイレクトされるかを検証
    $response->assertRedirect(route('diary.index'));

    // DBに保存されているかを検証
    $this->assertDatabaseHas('diaries', [
        'diary_id' => $before_diary->diary_id,
        'content' => $update_content['content'],
        'img_name' => $update_img->hashName(),
    ]);

    // 元のファイルが削除されていることを検証
    expect(Storage::disk('public')->exists('images/diaries/'.$before_diary->img_name))->toBeFalse();
    // ファイルが生成されていることを検証
    expect(Storage::disk('public')->exists('images/diaries/'.$update_img->hashName()))->toBeTrue();
});

it('画像なしで更新：本文だけが変わる、古い画像が残っている', function (): void {
    // 更新前のデータ作成
    $before_diary = Diary::factory()->create();
    Storage::disk('public')->put('images/diaries/'.$before_diary->img_name, 'dummy');

    // 画像なし更新用データ
    $update_content = [
        'content' => 'test投稿',
    ];

    // テスト対象メソッド実行
    $response = $this->put(route('diary.update', $before_diary), $update_content);

    // 成功時のセッションを検証
    $response->assertSessionHas('success', '日記を更新しました。');
    // 一覧にリダイレクトされるかを検証
    $response->assertRedirect(route('diary.index'));

    // DBに正しく保存されているかを検証
    $this->assertDatabaseHas('diaries', [
        'diary_id' => $before_diary->diary_id,
        'content' => $update_content['content'],
        'img_name' => $before_diary->img_name,
    ]);

    // 元のファイルが削除されていないことを検証
    expect(Storage::disk('public')->exists('images/diaries/'.$before_diary->img_name))->toBeTrue();
});

it('存在しないIDを指定：404になる', function (): void {
    // 更新前のデータ作成
    $before_diary = Diary::factory()->create();
    $dummy_id = $before_diary->diary_id + 1000;

    // 画像なし更新用データ
    $update_content = [
        'content' => 'test投稿',
    ];

    // テスト対象メソッド実行
    $response = $this->put(route('diary.update', $dummy_id), $update_content);
    // 404が返ってくる
    $response->assertNotFound();
});

it('contentが空だとバリデーションエラーになり更新されない', function (): void {
    // 更新前のデータ作成
    $before_diary = Diary::factory()->create();

    $update_content = [
        'content' => '',
        'image' => UploadedFile::fake()->image('update.jpg'),
    ];

    // テスト対象メソッド実行
    $response = $this->put(route('diary.update', $before_diary), $update_content);

    // contentのバリデーションエラーを検証
    $response->assertSessionHasErrors(['content']);

    // DBに保存されていないことを検証
    $this->assertDatabaseHas('diaries', [
        'diary_id' => $before_diary->diary_id,
        'content' => $before_diary->content,
    ]);
});

it('サービスで例外が発生：errorセッション、ログが出力される、DBが変わっていない', function (): void {
    Log::spy();
    // 更新前のデータ作成
    $before_diary = Diary::factory()->create();
    Storage::disk('public')->put('images/diaries/'.$before_diary->img_name, 'dummy');

    // 画像あり更新用データ
    $update_content = [
        'content' => 'test投稿',
        'image' => UploadedFile::fake()->image('update.jpg'),
    ];

    // モックして例外を発生させる
    $this->mock(DiaryService::class, function ($mock) {
        $mock->shouldReceive('update')
            ->once()
            ->andThrow(new Exception('DBエラー発生'));
    });

    // テスト対象メソッド実行(失敗)
    $response = $this->put(route('diary.update', $before_diary), $update_content);

    // 失敗時のセッションを検証
    $response->assertSessionHas('error', '日記を更新できませんでした。');

    // 一覧にリダイレクトされるかを検証
    $response->assertRedirect(route('diary.index'));

    // 更新されていないことを検証
    $this->assertDatabaseHas('diaries', [
        'diary_id' => $before_diary->diary_id,
        'content' => $before_diary->content,
    ]);
    // ログ出力を検証
    Log::shouldHaveReceived('error')->once();
});

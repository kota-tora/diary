<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    // 仮想ディスクに差し替え
    Storage::fake('public');
});

/**
 * 通常のランダム生成に戻す
 */
afterEach(function () {
    Str::createRandomStringsNormally();
});

it('有効なデータでPOSTすると一覧へリダイレクトされ、DBに登録される', function (): void {
    $test_img_file = UploadedFile::fake()->image('test.jpg');
    $test_content = 'テスト投稿';
    // テスト対象メソッド実行
    $response = $this->post(route('diary.store'), [
        'content' => $test_content,
        'image' => $test_img_file,
    ]);

    // 成功時のセッションを検証
    $response->assertSessionHas('success', '日記を登録しました。');
    // 一覧にリダイレクトされるかを検証
    $response->assertRedirect(route('diary.index'));

    // DBに保存されているかを検証
    $this->assertDatabaseHas('diaries', [
        'content' => $test_content,
        'img_name' => basename($test_img_file->hashName()),
    ]);
});

it('contentが空だとバリデーションエラーになり登録されない', function (): void {
    // テスト対象メソッド実行
    $response = $this->post(route('diary.store'), [
        'content' => '',
        'image' => UploadedFile::fake()->image('test.jpg'),
    ]);

    // contentのバリデーションエラーを検証
    $response->assertSessionHasErrors(['content']);

    // DBに保存されていないかを検証
    $this->assertDatabaseCount('diaries', 0);
});

it('登録処理が失敗した場合、一覧へリダイレクトされエラーメッセージとログが出る', function (): void {
    // ログ出力を検証するため
    Log::spy();
    // ランダム文字列を同じ文字列にし、ファイル名が重複するようにする
    Str::createRandomStringsUsing(fn () => 'duplicate_name');

    // 1件目
    $test_img_file = UploadedFile::fake()->image('test.jpg');
    $test_content = 'テスト投稿';
    // テスト対象メソッド実行
    $response = $this->post(route('diary.store'), [
        'content' => $test_content,
        'image' => $test_img_file,
    ]);

    // 2件目 imageがユニーク制約で例外を発生させる
    $test_second_img_file = UploadedFile::fake()->image('test2.jpg');
    $test_content = 'テスト投稿2';
    // テスト対象メソッド実行
    $response = $this->post(route('diary.store'), [
        'content' => $test_content,
        'image' => $test_second_img_file,
    ]);

    // 一覧にリダイレクトされるかを検証
    $response->assertRedirect(route('diary.index'));
    // エラーメッセージセッションを検証
    $response->assertSessionHas('error', '日記を登録できませんでした。');
    // DBに保存されていないことを検証
    $this->assertDatabaseMissing('diaries', ['content' => $test_content]);
    // ログ出力を検証
    Log::shouldHaveReceived('error')->once();
});

<?php

use App\Services\DiaryService;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
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

it('データが実際に保存され、画像ファイルが生成されるかのテスト', function ($file_name): void {
    $diary_service = app()->make(DiaryService::class);
    $test_img_file = UploadedFile::fake()->image($file_name);
    $test_content = 'テスト投稿';

    // テスト対象メソッド実行
    $diary_result = $diary_service->store($test_content, $test_img_file);

    // DBに保存されているかを検証
    $this->assertDatabaseHas('diaries', [
        'content' => $test_content,
        'img_name' => basename($test_img_file->hashName()),
    ]);

    // ファイルが保存されているか検証
    expect(Storage::disk('public')->exists('image/diaries/'.$diary_result->img_name))->tobe(true);
})->with(['test.jpg', 'test.jpeg', 'test.png', 'test.gif']);

it('contentが重複しても正常に保存できる', function (): void {
    $diary_service = app()->make(DiaryService::class);

    // 共通content
    $same_content = '同じ投稿テスト';

    // 1件目
    $test_img_file = UploadedFile::fake()->image('test.jpg');
    // 正常に処理が終了する
    $diary_success_result = $diary_service->store($same_content, $test_img_file);

    // DBに保存されているかを検証
    $this->assertDatabaseHas('diaries', [
        'content' => $same_content,
        'img_name' => basename($test_img_file->hashName()),
    ]);

    // ファイルが保存されているか検証
    expect(Storage::disk('public')->exists('image/diaries/'.$diary_success_result->img_name))->tobe(true);

    // 2件目
    $second_img_file = UploadedFile::fake()->image('test2.jpg');
    // DBの例外が発生していないことを検証する
    expect(fn () => $diary_service->store($same_content, $second_img_file))->not->toThrow(QueryException::class);

    // DBに保存されているかを検証
    $this->assertDatabaseHas('diaries', [
        'content' => $same_content,
        'img_name' => basename($second_img_file->hashName()),
    ]);

    // ファイルが保存されているか検証
    expect(Storage::disk('public')->exists('image/diaries/'.basename($second_img_file->hashName())))->tobe(true);
});

it('img_nameが重複するとDB保存に失敗し、例外が発生する', function (): void {
    // ランダム文字列を同じ文字列にし、ファイル名が重複するようにする
    Str::createRandomStringsUsing(fn () => 'duplicate_name');

    $diary_service = app()->make(DiaryService::class);

    // 1件目
    $test_success_content = '１件目テスト';
    $test_img_file = UploadedFile::fake()->image('test.jpg');
    // 正常に処理が終了する
    $diary_success_result = $diary_service->store($test_success_content, $test_img_file);

    // DBに保存されているかを検証
    $this->assertDatabaseHas('diaries', [
        'content' => $test_success_content,
        'img_name' => basename($test_img_file->hashName()),
    ]);

    // ファイルが保存されているか検証
    expect(Storage::disk('public')->exists('image/diaries/'.$diary_success_result->img_name))->tobe(true);

    // 2件目
    $test_fail_content = '2件目テスト';
    $fail_img_file = UploadedFile::fake()->image('fail.jpg');
    // DBのユニーク制約で例外が発生することを検証する
    expect(fn () => $diary_service->store($test_fail_content, $fail_img_file))->toThrow(QueryException::class);

    // DBに保存されていないかを検証
    $this->assertDatabaseMissing('diaries', [
        'content' => $test_fail_content,
        'img_name' => basename($fail_img_file->hashName()),
    ]);

    // ファイルが保存されているか検証
    expect(Storage::disk('public')->exists('image/diaries/'.basename($fail_img_file->hashName())))->tobe(true);
});

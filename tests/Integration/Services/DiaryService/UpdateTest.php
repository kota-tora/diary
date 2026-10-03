<?php

use App\Models\Diary;
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

it('画像ありで更新した場合、新しい画像が保存され、古い画像が削除される', function ($file_name) :void {
    // 更新前データ作成
    $before_diary = Diary::factory()->create();
    // 更新する前の画像ファイル名
    $old_file_name = $before_diary->img_name;
    // 更新する前の画像ファイルを生成する
    Storage::disk('public')->put('images/diaries/' . $old_file_name, 'dummy');

    // 更新する画像
    $update_image = UploadedFile::fake()->image($file_name);
    // 生成される画像ファイル名
    $img_file_name = $update_image->hashName();
    // 更新するデータ
    $update_params = ['content' => 'テスト投稿', 'img_name' => $img_file_name];
    $service = app()->make(DiaryService::class);
    // 画像ありで更新
    $result = $service->update($before_diary, $update_params['content'], $update_image);
    // 成功結果が返ってくることを検証
    expect($result)->toBeTrue();
    // 正しく更新されていることを検証
    $this->assertDatabaseHas('diaries', ['diary_id' => $before_diary->diary_id, ...$update_params]);

    // 元のファイルが削除されていることを検証
    expect(Storage::disk('public')->exists('images/diaries/' . $old_file_name))->toBeFalse();
    // ファイルが生成されていることを検証
    expect(Storage::disk('public')->exists('images/diaries/' . $update_image->hashName()))->toBeTrue();
})->with(['test.jpg', 'test.jpeg', 'test.png', 'test.gif']);

it('画像なしで更新した場合、contentのみ更新される', function ($file_name) :void {
    $diary = Diary::factory()->create();
    // 更新前の画像を生成しておく
    Storage::disk('public')->put('images/diaries/' . $diary->img_name, 'dummy');

    $update_params = ['content' => 'テスト投稿'];

    $service = app()->make(DiaryService::class);
    // 画像なしで更新
    $result = $service->update($diary, $update_params['content']);
    // 成功結果が返ってくることを検証
    expect($result)->toBeTrue();
    // 正しくcontentのみ更新されていることを検証
    $this->assertDatabaseHas('diaries', [
        'diary_id' => $diary->diary_id,
        'content' => $update_params['content'],
        'img_name' => $diary->img_name
    ]);
    // 元画像が削除されていない
    expect(Storage::disk('public')->exists('images/diaries/' . $diary->img_name))->toBeTrue();
})->with(['test.jpg', 'test.jpeg', 'test.png', 'test.gif']);

it('img_nameが重複するとDB保存に失敗し、例外が発生する', function (): void {
    // ランダム文字列を同じ文字列にし、ファイル名が重複するようにする
    Str::createRandomStringsUsing(fn () => 'duplicate_name');
    $diary_service = app()->make(DiaryService::class);

    // 1件目データ作成
    Diary::factory()->create([
        'img_name' => 'duplicate_name.jpg'
    ]);

    // 2件目のデータ作成
    $update_fail_content = '更新テスト';
    $second_diary = Diary::factory()->create();
    $test_img_file = UploadedFile::fake()->image('test.jpg');

    // img_nameが重複しDBのユニーク制約で例外が発生することを検証する
    expect(fn () => $diary_service->update($second_diary, $update_fail_content, $test_img_file))->toThrow(QueryException::class);

    // DBに保存されていないかを検証
    $this->assertDatabaseMissing('diaries', [
        'diary_id' => $second_diary->diary_id,
        'content' => $update_fail_content,
    ]);
});

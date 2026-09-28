<?php

use App\Models\Diary;
use App\Repository\DiaryRepository;
use App\Services\DiaryService;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use RuntimeException;

it('データ保存と画像ファイル生成テスト', function ($file_name): void {
    // 仮想ディスクに差し替え
    Storage::fake('public');
    $repository = Mockery::mock(DiaryRepository::class);

    $test_img_file = UploadedFile::fake()->image($file_name);
    $test_content = 'テスト投稿';
    // 生成される画像ファイル名
    $expect_img_name = basename($test_img_file->hashName());

    // モックしたDiaryRepositoryの検証
    $repository->shouldReceive('create')
        ->once()
        ->with([
            'img_name' => $expect_img_name,
            'content' => $test_content,
        ])
        ->andReturn(new Diary([
            'img_name' => $expect_img_name,
            'content' => $test_content,
        ]));

    $diary_service = new DiaryService($repository);
    $diary_result = $diary_service->store($test_content, $test_img_file);

    // ファイルが保存されているか検証
    expect(Storage::disk('public')->exists('image/diaries/'.$diary_result->img_name))->tobe(true);
})->with(['test.jpg', 'test.jpeg', 'test.png', 'test.gif']);

it('DBへの保存が失敗した場合のテスト', function (): void {
    Storage::fake('public');
    $repository = Mockery::mock(DiaryRepository::class);

    $test_img_file = UploadedFile::fake()->image('test.jpg');
    $test_content = 'テスト投稿';
    // 生成される画像ファイル名
    $expect_img_name = basename($test_img_file->hashName());

    // モックしたDiaryRepositoryの検証
    $repository->shouldReceive('create')
        ->once()
        ->andThrow(new RuntimeException('DB error'));

    $diary_service = new DiaryService($repository);
    // 例外がスローされることを検証
    expect(fn () => $diary_service->store($test_content, $test_img_file))->toThrow(Exception::class);

    // ファイルが保存されているか検証
    expect(Storage::disk('public')->exists('image/diaries/'.$expect_img_name))->tobe(true);
});

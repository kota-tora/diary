<?php

use App\Models\Diary;
use App\Repository\DiaryRepository;
use App\Services\DiaryService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // 仮想ディスクに差し替え
    Storage::fake('public');
});

it('データ保存と画像ファイルが生成される', function ($file_name): void {
    $repository = Mockery::mock(DiaryRepository::class);

    $test_img_file = UploadedFile::fake()->image($file_name);
    $test_content = 'テスト投稿';
    // 生成される画像ファイル名
    $expect_img_name = $test_img_file->hashName();

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
    expect(Storage::disk('public')->exists('images/diaries/'.$diary_result->img_name))->tobe(true);
})->with(['test.jpg', 'test.jpeg', 'test.png', 'test.gif']);

it('DBへの保存が失敗した場合に例外が発生し、ファイルも作られない', function (): void {
    $repository = Mockery::mock(DiaryRepository::class);

    $test_img_file = UploadedFile::fake()->image('test.jpg');
    $test_content = 'テスト投稿';
    // 生成される画像ファイル名
    $expect_img_name = $test_img_file->hashName();

    // モックしたDiaryRepositoryの検証
    $repository->shouldReceive('create')
        ->once()
        ->andThrow(new RuntimeException('DB error'));

    $diary_service = new DiaryService($repository);
    // 例外がスローされることを検証
    expect(fn () => $diary_service->store($test_content, $test_img_file))->toThrow(Exception::class, 'DB error');

    // ファイルが保存されていないことを検証
    expect(Storage::disk('public')->exists('images/diaries/' . $expect_img_name))->toBeFalse();
});

it('画像の保存が失敗した場合に例外が発生する', function (): void {
    $uploaded_file = Mockery::mock(UploadedFile::class);
    // 画像アップロードに失敗するようにモックする
    $uploaded_file->shouldReceive('store')
        ->once()
        ->andReturn(false);

    // 画像ファイル作成に失敗し、createメソッドが呼ばれないモック
    $repository = Mockery::mock(DiaryRepository::class);
    $repository->shouldNotReceive('create');
        
    $service = new DiaryService($repository);
    // テスト対象メソッド実行し例外が返ってくるかを検証
    expect(fn() => $service->store('テスト投稿', $uploaded_file))->toThrow(Exception::class, '画像の保存に失敗しました');
});

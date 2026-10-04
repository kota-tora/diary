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

it('画像なしで更新した場合、contentのみ更新される', function (): void {
    $repository = Mockery::mock(DiaryRepository::class);
    $diary = Diary::factory()->create();
    // 更新前の画像を生成しておく
    Storage::disk('public')->put('images/diaries/'.$diary->img_name, 'dummy');

    $update_params = ['content' => 'テスト投稿'];
    // repositoryをモック
    $repository->shouldReceive('update')
        ->once()
        ->with($diary, $update_params)
        ->andReturn(true);

    $service = new DiaryService($repository);
    // 画像なしで更新
    $result = $service->update($diary, $update_params['content']);
    // 成功結果が返ってくることを検証
    expect($result)->toBeTrue();
    // 元画像が削除されていない
    expect(Storage::disk('public')->exists('images/diaries/'.$diary->img_name))->toBeTrue();
});

it('画像ありで更新した場合、新しい画像が保存され、古い画像が削除される', function ($file_name): void {
    $repository = Mockery::mock(DiaryRepository::class);

    // 更新前データ作成
    $before_diary = Diary::factory()->create();
    // 更新する前の画像ファイルを生成する
    Storage::disk('public')->put('images/diaries/'.$before_diary->img_name, 'dummy');

    // 更新する画像
    $update_image = UploadedFile::fake()->image($file_name);
    // 生成される画像ファイル名
    $img_file_name = basename($update_image->hashName());
    // 更新するデータ
    $update_params = ['content' => 'テスト投稿', 'img_name' => $img_file_name];

    // repositoryをモック
    $repository->shouldReceive('update')
        ->once()
        ->with($before_diary, $update_params)
        ->andReturn(true);

    $service = new DiaryService($repository);
    // 画像ありで更新
    $result = $service->update($before_diary, $update_params['content'], $update_image);
    // 成功結果が返ってくることを検証
    expect($result)->toBeTrue();
    // 元のファイルが削除されていることを検証
    expect(Storage::disk('public')->exists('images/diaries/'.basename($before_diary->img_name)))->toBeFalse();
    // ファイルが生成されていることを検証
    expect(Storage::disk('public')->exists('images/diaries/'.basename($update_image->hashName())))->toBeTrue();
})->with(['test.jpg', 'test.jpeg', 'test.png', 'test.gif']);

it('DB更新が失敗した場合、例外が返ってくる', function (): void {
    // 更新前データ作成
    $before_diary = Diary::factory()->create();
    $update_content = 'テスト投稿';
    // repositoryをモック
    $repository = Mockery::mock(DiaryRepository::class);
    $repository->shouldReceive('update')
        ->once()
        ->andThrow(new Exception('DBエラー発生'));

    $service = new DiaryService($repository);
    // テスト対象メソッド実行し例外が返ってくるかを検証
    expect(fn () => $service->update($before_diary, $update_content))->toThrow(Exception::class, 'DBエラー発生');
});

it('画像ありでDB更新に失敗した場合、新しい画像が追加されず元の画像が残っている', function (): void {
    // 更新前データ作成
    $before_diary = Diary::factory()->create();
    // 更新する前の画像ファイルを生成する
    Storage::disk('public')->put('images/diaries/'.$before_diary->img_name, 'dummy');

    // 更新する画像
    $update_image = UploadedFile::fake()->image('test2.jpg');
    $update_content = '更新テスト';

    // repositoryをモック
    $repository = Mockery::mock(DiaryRepository::class);
    $repository->shouldReceive('update')
        ->once()
        ->andThrow(new Exception('DBエラー発生'));

    $service = new DiaryService($repository);
    // テスト対象メソッド実行し例外が返ってくるかを検証
    expect(fn () => $service->update($before_diary, $update_content, $update_image))->toThrow(Exception::class, 'DBエラー発生');

    // 更新用の画像が作成されていないことを検証
    expect(Storage::disk('public')->exists('images/diaries/'.$update_image->hashName()))->toBeFalse();

    // 元の画像が削除されていないことを検証
    expect(Storage::disk('public')->exists('images/diaries/'.$before_diary->img_name))->toBeTrue();
});

it('画像の保存に失敗した場合、例外が投げられ古い画像が残っている', function (): void {
    $uploaded_file = Mockery::mock(UploadedFile::class);
    // 画像アップロードに失敗するようにモックする
    $uploaded_file->shouldReceive('store')
        ->once()
        ->andReturn(false);

    // 更新前データ作成
    $before_diary = Diary::factory()->create();
    // 更新する前の画像ファイルを生成する
    Storage::disk('public')->put('images/diaries/'.$before_diary->img_name, 'dummy');

    // 更新データ
    $update_content = '更新テスト';

    // 画像ファイル作成に失敗し、updateメソッドが呼ばれないモック
    $repository = Mockery::mock(DiaryRepository::class);
    $repository->shouldNotReceive('update');

    $service = new DiaryService($repository);
    // テスト対象メソッド実行し例外が返ってくるかを検証
    expect(fn () => $service->update($before_diary, $update_content, $uploaded_file))->toThrow(Exception::class, '画像の保存に失敗しました');

    // 元の画像が削除されていないことを検証
    expect(Storage::disk('public')->allFiles())->toBe(['images/diaries/'.$before_diary->img_name]);
});

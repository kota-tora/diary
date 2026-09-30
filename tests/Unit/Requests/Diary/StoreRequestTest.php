<?php

use App\Http\Requests\Diary\StoreRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

/**
 * バリデーションを実行し、結果を返す
 *
 * @param: string $content
 *
 * @param: string $img_file_name
 */
function storeRequestValidate(string $content, $img_file_name = 'test.jpg'): Illuminate\Validation\Validator
{
    // バリデーションルール取得
    $rules = (new StoreRequest)->rules();
    // テストファイルを作成
    $test_img_file = UploadedFile::fake()->image($img_file_name);

    // バリデーション実行
    return Validator::make(['content' => $content, 'image' => $test_img_file], $rules);
}

it('contentが3〜30文字ならバリデーション通過するかを検証するテスト', function (string $content): void {
    // バリデーション実行
    $validator = storeRequestValidate($content);
    // バリデーションが通ったかを検証
    expect($validator->passes())->toBeTrue();
})->with([
    str_repeat('あ', 3),
    str_repeat('あ', 30),
]);

it('contentが3文字未満ならバリデーション通過しないかを検証するテスト', function (string $content): void {
    // バリデーション実行
    $validator = storeRequestValidate($content);
    // バリデーションが通過しないかを検証
    expect($validator->passes())->toBeFalse();
})->with([
    '',
    'あ',
    'ああ',
]);

it('contentが31文字以上だとバリデーション通過しないかを検証するテスト', function (string $content): void {
    // バリデーション実行
    $validator = storeRequestValidate($content);
    // バリデーションが通過しないかを検証
    expect($validator->passes())->toBeFalse();
})->with([
    str_repeat('あ', 31),
    str_repeat('あ', 40),
]);

it('imageにファイルではなくstringを渡すとバリデーション通過しないかを検証するテスト', function (): void {
    // バリデーションルール取得
    $rules = (new StoreRequest)->rules();
    // imageにファイルではなくstringを渡しバリデーション実行
    $validator = Validator::make(['content' => str_repeat('あ', 10), 'image' => 'ダミーテキスト'], $rules);

    expect($validator->passes())->toBeFalse();
});

it('imageが未設定だとバリデーション通過しないかを検証するテスト', function (): void {
    // バリデーションルール取得
    $rules = (new StoreRequest)->rules();
    // imageが未設定でバリデーション実行
    $validator = Validator::make(['content' => str_repeat('あ', 10)], $rules);

    expect($validator->passes())->toBeFalse();
});

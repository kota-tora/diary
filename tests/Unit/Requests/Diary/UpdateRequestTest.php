<?php

use App\Http\Requests\Diary\UpdateRequest;
use App\Models\Diary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

/**
 * バリデーションを実行し、結果を返す
 *
 * @param: string $content
 *
 * @param: string $img_file_name
 */
function updateRequestValidate(string $content, $img_file_name = ''): Illuminate\Validation\Validator
{
    // バリデーションルール取得
    $rules = (new UpdateRequest())->rules();
    // テストファイルを作成
    $test_img_file = $img_file_name ? UploadedFile::fake()->image($img_file_name) : '';

    // バリデーション実行
    return Validator::make(['content' => $content, 'image' => $test_img_file], $rules);
}

it('contentが3〜30文字ならバリデーション通過するか', function (string $content): void {
    // バリデーション実行
    $validator = updateRequestValidate($content, 'test.jpg');
    // バリデーションが通ったかを検証
    expect($validator->passes())->toBeTrue();
})->with([
    str_repeat('あ', Diary::CONTENT_MIN_LENGTH),
    str_repeat('あ', Diary::CONTENT_MAX_LENGTH),
]);

it('contentが3文字未満ならバリデーション通過しないか', function (string $content): void {
    // バリデーション実行
    $validator = updateRequestValidate($content, 'test.jpg');
    // バリデーションが通過しないかを検証
    expect($validator->passes())->toBeFalse();
})->with([
    '',
    'あ',
    'ああ',
]);

it('contentが31文字以上だとバリデーション通過しないか', function (string $content): void {
    // バリデーション実行
    $validator = updateRequestValidate($content, 'test.jpg');
    // バリデーションが通過しないかを検証
    expect($validator->passes())->toBeFalse();
})->with([
    str_repeat('あ', Diary::CONTENT_MAX_LENGTH + 1),
    str_repeat('あ', Diary::CONTENT_MAX_LENGTH + 10),
]);

it('contentのキーがないとバリデーション通過しないか', function (): void {
    // バリデーションルール取得
    $rules = (new UpdateRequest())->rules();
    $test_img_file = UploadedFile::fake()->image('test.jpg');
    // contentを除外して、バリデーション実行
    $validator = Validator::make(['image' => $test_img_file], $rules);

    expect($validator->passes())->toBeFalse()
        ->and($validator->errors()->has('content'))->toBeTrue();
});

it('imageにファイルではなくstringを渡すとバリデーション通過しないか', function (): void {
    // バリデーションルール取得
    $rules = (new UpdateRequest())->rules();
    // imageにファイルではなくstringを渡しバリデーション実行
    $validator = Validator::make(['content' => str_repeat('あ', 10), 'image' => 'ダミーテキスト'], $rules);

    expect($validator->passes())->toBeFalse();
});

it('imageが空でもバリデーション通過するか', function (): void {
    // 画像未設定でバリデーション実行
    $validator = updateRequestValidate(str_repeat('あ', 20));

    expect($validator->passes())->toBeTrue();
});

it('imageのキーがなくてもバリデーション通過するか', function (): void {
    // バリデーションルール取得
    $rules = (new UpdateRequest())->rules();
    // imageを除外して、バリデーション実行
    $validator = Validator::make(['content' => str_repeat('あ', 10)], $rules);

    expect($validator->passes())->toBeTrue();
});

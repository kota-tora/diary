<?php

namespace App\Services;

use App\Models\Diary;
use App\Repository\DiaryRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DiaryService
{
    protected $diary_repository;

    public function __construct(DiaryRepository $diary_repository)
    {
        $this->diary_repository = $diary_repository;
    }

    /**
     * 日記と画像ファイルの保存処理
     */
    public function store(string $content, UploadedFile $image): Model
    {
        return DB::transaction(function () use ($content, $image) {
            // 画像ファイルを保存
            $file_path = $image->store('images/diaries', 'public');

            if (!$file_path) {
                throw new Exception('画像の保存に失敗しました');
            }

            // DB保存処理に失敗した場合
            DB::afterRollBack(function () use ($file_path) {
                // 追加されたファイルの削除処理
                Storage::disk('public')->delete($file_path);
            });

            // DB保存処理
            return $this->diary_repository->create([
                // ファイル名のみ保存
                'img_name' => basename($file_path),
                'content' => $content,
            ]);
        });
    }

    /**
     * 日記更新と画像ファイルの生成と旧ファイルの削除処理
     */
    public function update(Diary $before_diary, string $content, ?UploadedFile $image = null): bool
    {
        return DB::transaction(function () use ($before_diary, $content, $image) {
            $updates = [
                'content' => $content,
            ];

            if ($image) {
                // 画像ファイルを保存
                $file_path = $image->store('images/diaries', 'public');

                if (!$file_path) {
                    throw new Exception('画像の保存に失敗しました');
                }

                $updates['img_name'] = basename($file_path);
                // 古い画像ファイル名を保存しておく
                $old_img_name = $before_diary->img_name;
                // 更新処理完了後
                DB::afterCommit(function () use ($old_img_name) {
                    // 古いファイルの削除処理
                    Storage::disk('public')->delete('images/diaries/' . $old_img_name);
                });
                // DB更新処理に失敗した場合
                DB::afterRollBack(function () use ($file_path) {
                    // 新規追加されたファイルの削除処理
                    Storage::disk('public')->delete($file_path);
                });
            }
            // DB保存処理
            return $this->diary_repository->update($before_diary, $updates);
        });
    }
}

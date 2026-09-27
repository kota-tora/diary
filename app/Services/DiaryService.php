<?php

namespace App\Services;

use App\Repository\DiaryRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

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
    public function store(string $content, UploadedFile $image)
    {
        DB::transaction(function () use ($content, $image) {
            // 画像ファイルを保存
            $file_path = $image->store('image/diaries', 'public');

            // DB保存処理
            $this->diary_repository->create([
                // ファイル名のみ保存
                'img_name' => basename($file_path),
                'content' => $content,
            ]);
        });
    }
}

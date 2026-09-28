<?php

namespace App\Http\Controllers;

use App\Http\Requests\Diary\StoreRequest;
use App\Services\DiaryService;
use Exception;
use Illuminate\Support\Facades\Log;

class DiaryController extends Controller
{
    protected $diary_service;

    public function __construct(DiaryService $diary_service)
    {
        $this->diary_service = $diary_service;
    }

    /**
     * 一覧画面
     */
    public function index()
    {
        return view('lists.index');
    }

    /**
     * 日記新規登録画面
     */
    public function create()
    {
        return view('create.index');
    }

    /**
     * 日記登録処理
     */
    public function store(StoreRequest $store_request)
    {
        try {
            $post = $store_request->validated();
            // 画像保存と日記登録処理
            // 処理失敗時は例外発生で分岐し、戻り値の検証はしない
            $this->diary_service->store($post['content'], $store_request->file('image'));

            // 一覧へ
            return redirect(route('diary.index'))->with('success', '日記を登録しました。');
        } catch (Exception $e) {
            Log::error('日記登録処理に失敗しました。', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('diary.index')->with('error', '日記を登録できませんでした。');
        }
    }
}

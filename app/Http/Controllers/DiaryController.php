<?php

namespace App\Http\Controllers;

use App\Http\Requests\Diary\StoreRequest;
use App\Http\Requests\Diary\UpdateRequest;
use App\Models\Diary;
use App\Repository\DiaryRepository;
use App\Services\DiaryService;
use Exception;
use Illuminate\Support\Facades\Log;

class DiaryController extends Controller
{
    protected $diary_service;

    protected $diary_repository;

    public function __construct(DiaryService $diary_service, DiaryRepository $diary_repository)
    {
        $this->diary_service = $diary_service;
        $this->diary_repository = $diary_repository;
    }

    /**
     * 一覧画面
     */
    public function index()
    {
        // 日記データをページネーションでID降順で取得する
        $rows = $this->diary_repository->getPaginatedOrderedById(true);

        return view('diaries.index', compact('rows'));
    }

    /**
     * 追加画面
     */
    public function create()
    {
        $is_update = false;

        return view('diaries.create', compact('is_update'));
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

    /**
     * 編集画面
     */
    public function edit(Diary $diary)
    {
        $is_update = true;

        return view('diaries.create', compact('is_update', 'diary'));
    }

    /**
     * 更新処理
     */
    public function update(UpdateRequest $update_request, Diary $diary)
    {
        try {
            $post = $update_request->validated();
            // 画像と日記更新処理
            // 処理失敗時は例外発生で分岐し、戻り値の検証はしない
            $this->diary_service->update($diary, $post['content'], $update_request->file('image'));

            // 一覧へ
            return redirect(route('diary.index'))->with('success', '日記を更新しました。');
        } catch (Exception $e) {
            Log::error('日記更新処理に失敗しました。', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('diary.index')->with('error', '日記を更新できませんでした。');
        }
    }

    /**
     * 削除処理
     */
    public function destroy(Diary $diary)
    {
        try {
            // 削除
            $this->diary_repository->delete($diary);

            // 一覧へ
            return redirect(route('diary.index'))->with('success', '日記を削除しました。');
        } catch (Exception $e) {
            Log::error('日記削除に失敗しました。', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('diary.index')->with('error', '日記を削除できませんでした。');
        }
    }
}

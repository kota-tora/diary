@props([
    'route',
    'id'
])

<div>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $id }}">
        削除
    </button>

    <!-- Modal -->
    <div class="modal fade" id="deleteModal{{ $id }}" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="deleteModal$idLabel">削除確認</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <span class="fw-bold">本当に削除しますか？</span>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
            <form method="post" action="{{ $route }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">削除する</button>
            </form>
        </div>
        </div>
    </div>
    </div>
</div>

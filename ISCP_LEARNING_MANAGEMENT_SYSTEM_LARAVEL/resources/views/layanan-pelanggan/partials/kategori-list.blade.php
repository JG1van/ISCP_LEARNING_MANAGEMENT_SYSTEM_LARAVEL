<ul class="list-group">
    @if ($items->count() === 0)
        <li class="list-group-item text-danger">
            Kategori ini belum tersedia, atau atau Anda tidak memiliki akses hak akses untuk melihat konten ini.
        </li>
    @else
        @foreach ($items as $cat)
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                    <div class="fw-bold">
                        {{ $cat->name }}
                        <span class="text-muted">({{ $cat->level }})</span>
                    </div>
                </div>

                <button class="btn btn-add " onclick='openProblemDetail(@json($cat->id))'>
                    Pilih
                </button>
            </li>
        @endforeach
    @endif
</ul>

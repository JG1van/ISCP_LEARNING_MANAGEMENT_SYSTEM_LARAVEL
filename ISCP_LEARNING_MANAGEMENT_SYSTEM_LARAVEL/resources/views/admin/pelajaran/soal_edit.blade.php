@extends('admin.layouts.app')

@section('title', 'Edit Soal ')
@section('page_title', 'Edit Soal ')

@section('content')
    <div class="container-fluid">
        <form id="formSoal"
            action="{{ route('admin.pelajaran.judul_soal.soal.update', [$lesson->id, $exercise->id, $item->id]) }}"
            method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="selection" id="selectionInput">
            <input type="hidden" name="exercise_model_id" value="{{ $item->exercise_model_id }}">
            {{-- <input type="hidden" name="exercise_competence_id" value="{{ $item->exercise_competence_id }}"> --}}

            <div class="col-lg-12 col-md-12 bg-white p-3 shadow-sm mb-3 rounded">
                <h5 class="fw-bold mb-3 text-uppercase">Informasi Soal</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Tipe Soal</label>
                        <input type="text" class="form-control" value="{{ $exerciseType->name }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kompetensi (KD)</label>
                        <select class="form-select" name="competence_id">
                            <option value="">== Pilih Kompetensi ==</option>
                            @foreach ($competences as $competence)
                                <option value="{{ $competence->id }}"
                                    {{ $item->competence_id == $competence->id ? 'selected' : '' }}>
                                    {{ $competence->point }} - {{ $competence->description }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Model Soal</label>
                        <select class="form-select" name="exercise_model_id" disabled>
                            @foreach ($models as $model)
                                <option value="{{ $model->id }}"
                                    {{ $item->exercise_model_id == $model->id ? 'selected' : '' }}>
                                    {{ $model->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="exercise_choice" class="form-label fw-semibold">Jumlah Pilihan</label>
                        <select id="exercise_choice" name="exercise_choice" class="form-select" onchange="buatPilihan()"
                            disabled>
                            <option value="">== Pilih ==</option>
                            @foreach ([3, 4, 6, 8, 10] as $jumlah)
                                <option value="{{ $jumlah }}"
                                    {{ is_array($item->selection) && count($item->selection) == $jumlah ? 'selected' : '' }}>
                                    {{ $jumlah }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-12 mt-2">
                        <label class="form-label fw-semibold">Nomor Soal</label>
                        <input type="number" name="exercise_number" value="{{ $item->exercise_number }}"
                            class="form-control" min="1" required>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 col-md-12 bg-white p-3 shadow-sm rounded">
                <h5 class="fw-bold mb-3 text-uppercase">Isi Soal</h5>
                <div id="formArea"></div>

                <div class="mt-4 text-end">
                    <button type="submit" id="btnSimpan" class="btn btn-add w-100">Update Soal</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Quill --}}
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script>
        const toolbar = [
            [{
                font: []
            }, {
                size: []
            }],
            ["bold", "italic", "underline", "strike"],
            [{
                color: []
            }, {
                background: []
            }],
            [{
                list: "ordered"
            }, {
                list: "bullet"
            }],
            [{
                align: []
            }]
        ];

        let editors = [];

        function createEditor(id, content = "", height = 150) {
            const el = document.getElementById(id);
            if (!el) return;
            el.style.height = height + "px";
            const q = new Quill(`#${id}`, {
                theme: "snow",
                modules: {
                    toolbar
                }
            });
            q.root.innerHTML = content || "";
            editors.push(q);
        }

        function loadExistingForm() {
            const model = {{ $item->exercise_model_id }};
            const area = document.getElementById("formArea");
            const selections = @json(is_array($item->selection) ? $item->selection : json_decode($item->selection ?? '[]', true));
            const answer = @json(is_array($item->answer) ? $item->answer : json_decode($item->answer ?? '[]', true));
            const question = `{!! $item->question !!}`;
            area.innerHTML = "";

            // --- MODEL PILIHAN GANDA / GANDA BANYAK ---
            if (model == 1 || model == 2) {
                let jumlah = selections.length;
                let html = `
            <label>Pertanyaan:</label>
            <div id="editorQuestion" class="border p-2 rounded"></div>
            <input type="hidden" name="question" id="hiddenQuestion">
            <h6 class="mt-3">Pilihan Jawaban</h6>
            <table class="table table-bordered text-center align-middle">
                <thead><tr><th>Abjad</th><th>Pilihan</th></tr></thead><tbody>`;
                for (let i = 0; i < jumlah; i++) {
                    const huruf = String.fromCharCode(65 + i);
                    html += `<tr><td class="fw-bold">${huruf}</td>
                <td><div id="opt${huruf}" class="border rounded p-2"></div></td></tr>`;
                }
                html += `</tbody></table>`;

                if (model == 1) {
                    html += `<label class="fw-semibold">Jawaban Benar:</label>
                <select id="answer" name="answer" class="form-select" required>`;
                    for (let i = 0; i < jumlah; i++) {
                        const huruf = String.fromCharCode(65 + i);
                        html +=
                            `<option value="${huruf}" ${(Array.isArray(answer) ? answer.includes(huruf) : answer == huruf) ? 'selected' : ''}>${huruf}</option>`;
                    }
                    html += `</select>`;
                } else {
                    html += `<label class="fw-semibold">Jawaban Benar:</label>
                <div class="d-flex flex-wrap gap-2">`;
                    for (let i = 0; i < jumlah; i++) {
                        const huruf = String.fromCharCode(65 + i);
                        html += `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="answer[]" value="${huruf}" id="ans${huruf}"
                        ${(Array.isArray(answer) && answer.includes(huruf)) ? 'checked' : ''}>
                        <label class="form-check-label" for="ans${huruf}">${huruf}</label>
                    </div>`;
                    }
                    html += `</div>`;
                }
                area.innerHTML = html;

                createEditor("editorQuestion", question);
                setTimeout(() => {
                    for (let i = 0; i < jumlah; i++) {
                        const huruf = String.fromCharCode(65 + i);
                        createEditor(`opt${huruf}`, selections[i] ?? "", 100);
                    }
                }, 200);

                // --- MODEL ISIAN ---
            } else if (model == 4) {
                area.innerHTML =
                    `
            <label>Pertanyaan:</label>
            <div id="editorQuestion" class="border p-2 rounded"></div>
            <input type="hidden" name="question" id="hiddenQuestion">
            <label class="mt-3">Jawaban:</label>
            <input type="text" name="answer" value="${answer ?? ''}" class="form-control" required autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">`;
                createEditor("editorQuestion", question);

                // --- MODEL URAIAN / ARGUMEN ---
            } else if (model == 5 || model == 7) {
                area.innerHTML = `
            <label>Pertanyaan:</label>
            <div id="editorQuestion" class="border p-2 rounded"></div>
            <input type="hidden" name="question" id="hiddenQuestion">
            <label class="mt-3">${model == 5 ? 'Panduan Penilaian' : 'Argumen / Jawaban'}:</label>
            <div id="editorAnswer" class="border p-2 rounded"></div>
            <input type="hidden" name="answer" id="hiddenAnswer" required>`;
                createEditor("editorQuestion", question);
                createEditor("editorAnswer", answer ?? "");

                // --- MODEL IYA/TIDAK ---
            } else if (model == 6) {
                area.innerHTML = `
            <label>Pertanyaan:</label>
            <div id="editorQuestion" class="border p-2 rounded"></div>
            <input type="hidden" name="question" id="hiddenQuestion">
            <label class="mt-3">Jawaban:</label>
            <select id="answer" name="answer" class="form-select">
                <option value="Iya" ${(answer == "Iya") ? 'selected' : ''}>Iya</option>
                <option value="Tidak" ${(answer == "Tidak") ? 'selected' : ''}>Tidak</option>
            </select>`;
                createEditor("editorQuestion", question);

                // --- MODEL PERNYATAAN ---
            } else if (model == 3) {
                area.innerHTML = `
            <label>Pernyataan:</label>
            <div id="editorQuestion" class="border p-2 rounded"></div>
            <input type="hidden" name="question" id="hiddenQuestion">
            <label class="mt-3">Kunci Jawaban:</label>
            <select id="answer" name="answer" class="form-select">
                <option value="Benar" ${(answer == "Benar") ? 'selected' : ''}>Benar</option>
                <option value="Salah" ${(answer == "Salah") ? 'selected' : ''}>Salah</option>
            </select>`;
                createEditor("editorQuestion", question);
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            loadExistingForm();

            document.getElementById('formSoal').addEventListener('submit', e => {
                const qEditor = editors.find(q => q.container.id === "editorQuestion");
                if (qEditor) document.getElementById("hiddenQuestion").value = qEditor.root.innerHTML
                    .trim();

                const aEditor = editors.find(q => q.container.id === "editorAnswer");
                if (aEditor && document.getElementById("hiddenAnswer"))
                    document.getElementById("hiddenAnswer").value = aEditor.root.innerHTML.trim();

                const selections = [];
                for (let q of editors) {
                    if (q.container.id.startsWith("opt")) selections.push(q.root.innerHTML.trim());
                }
                document.getElementById('selectionInput').value = JSON.stringify(selections);
            });
        });
    </script>
@endsection
@section('js')
    <script>
        const toolbar = [
            [{
                font: []
            }, {
                size: []
            }],
            ["bold", "italic", "underline", "strike"],
            [{
                color: []
            }, {
                background: []
            }],
            [{
                list: "ordered"
            }, {
                list: "bullet"
            }],
            [{
                align: []
            }]
        ];

        let editors = [];

        function createEditor(id, content = "", height = 150) {
            const el = document.getElementById(id);
            if (!el) return;
            el.style.height = height + "px";
            const q = new Quill(`#${id}`, {
                theme: "snow",
                modules: {
                    toolbar
                }
            });
            q.root.innerHTML = content || "";
            editors.push(q);
        }

        function loadExistingForm() {
            const model = {{ $item->exercise_model_id }};
            const area = document.getElementById("formArea");
            const selections = @json(is_array($item->selection) ? $item->selection : json_decode($item->selection ?? '[]', true));
            const answer = @json(is_array($item->answer) ? $item->answer : json_decode($item->answer ?? '[]', true));
            const question = `{!! $item->question !!}`;
            area.innerHTML = "";

            // --- MODEL PILIHAN GANDA / GANDA BANYAK ---
            if (model == 1 || model == 2) {
                let jumlah = selections.length;
                let html = `
                <label>Pertanyaan:</label>
                <div id="editorQuestion" class="border p-2 rounded"></div>
                <input type="hidden" name="question" id="hiddenQuestion">
                <h6 class="mt-3">Pilihan Jawaban</h6>
                <table class="table table-bordered text-center align-middle">
                    <thead><tr><th>Abjad</th><th>Pilihan</th></tr></thead><tbody>`;

                for (let i = 0; i < jumlah; i++) {
                    const huruf = String.fromCharCode(65 + i);
                    html += `
                    <tr>
                        <td class="fw-bold">${huruf}</td>
                        <td><div id="opt${huruf}" class="border rounded p-2"></div></td>
                    </tr>`;
                }
                html += `</tbody></table>`;

                if (model == 1) {
                    html += `
                    <label class="fw-semibold">Jawaban Benar:</label>
                    <select id="answer" name="answer" class="form-select" required>`;
                    for (let i = 0; i < jumlah; i++) {
                        const huruf = String.fromCharCode(65 + i);
                        html +=
                            `<option value="${huruf}" ${(Array.isArray(answer) ? answer.includes(huruf) : answer == huruf) ? 'selected' : ''}>${huruf}</option>`;
                    }
                    html += `</select>`;
                } else {
                    html += `
                    <label class="fw-semibold">Jawaban Benar:</label>
                    <div class="d-flex flex-wrap gap-2">`;
                    for (let i = 0; i < jumlah; i++) {
                        const huruf = String.fromCharCode(65 + i);
                        html += `
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="answer[]" value="${huruf}" id="ans${huruf}" ${(Array.isArray(answer) && answer.includes(huruf)) ? 'checked' : ''}>
                            <label class="form-check-label" for="ans${huruf}">${huruf}</label>
                        </div>`;
                    }
                    html += `</div>`;
                }

                area.innerHTML = html;
                createEditor("editorQuestion", question);

                setTimeout(() => {
                    for (let i = 0; i < jumlah; i++) {
                        const huruf = String.fromCharCode(65 + i);
                        createEditor(`opt${huruf}`, selections[i] ?? "", 100);
                    }
                }, 200);

            } else if (model == 4) {
                // --- MODEL ISIAN ---
                area.innerHTML = `
                <label>Pertanyaan:</label>
                <div id="editorQuestion" class="border p-2 rounded"></div>
                <input type="hidden" name="question" id="hiddenQuestion">
                <label class="mt-3">Jawaban:</label>
                <input type="text" name="answer" value="${answer ?? ''}" class="form-control" required>`;
                createEditor("editorQuestion", question);

            } else if (model == 5 || model == 7) {
                // --- MODEL URAIAN / ARGUMEN ---
                area.innerHTML = `
                <label>Pertanyaan:</label>
                <div id="editorQuestion" class="border p-2 rounded"></div>
                <input type="hidden" name="question" id="hiddenQuestion">
                <label class="mt-3">${model == 5 ? 'Panduan Penilaian' : 'Argumen / Jawaban'}:</label>
                <div id="editorAnswer" class="border p-2 rounded"></div>
                <input type="hidden" name="answer" id="hiddenAnswer" required>`;
                createEditor("editorQuestion", question);
                createEditor("editorAnswer", answer ?? "");

            } else if (model == 6) {
                // --- MODEL IYA / TIDAK ---
                area.innerHTML = `
                <label>Pertanyaan:</label>
                <div id="editorQuestion" class="border p-2 rounded"></div>
                <input type="hidden" name="question" id="hiddenQuestion">
                <label class="mt-3">Jawaban:</label>
                <select id="answer" name="answer" class="form-select">
                    <option value="Iya" ${(answer == "Iya") ? 'selected' : ''}>Iya</option>
                    <option value="Tidak" ${(answer == "Tidak") ? 'selected' : ''}>Tidak</option>
                </select>`;
                createEditor("editorQuestion", question);

            } else if (model == 3) {
                // --- MODEL PERNYATAAN ---
                area.innerHTML = `
                <label>Pernyataan:</label>
                <div id="editorQuestion" class="border p-2 rounded"></div>
                <input type="hidden" name="question" id="hiddenQuestion">
                <label class="mt-3">Kunci Jawaban:</label>
                <select id="answer" name="answer" class="form-select">
                    <option value="Benar" ${(answer == "Benar") ? 'selected' : ''}>Benar</option>
                    <option value="Salah" ${(answer == "Salah") ? 'selected' : ''}>Salah</option>
                </select>`;
                createEditor("editorQuestion", question);
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            loadExistingForm();

            const form = document.getElementById('formSoal');
            const btn = document.getElementById('btnSimpan');

            form.addEventListener('submit', e => {
                // 🔹 disable tombol & tampilkan spinner
                btn.setAttribute('disabled', true);
                const originalHTML = btn.innerHTML;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...`;

                const qEditor = editors.find(q => q.container.id === "editorQuestion");
                if (qEditor) document.getElementById("hiddenQuestion").value = qEditor.root.innerHTML
                    .trim();

                const aEditor = editors.find(q => q.container.id === "editorAnswer");
                if (aEditor && document.getElementById("hiddenAnswer"))
                    document.getElementById("hiddenAnswer").value = aEditor.root.innerHTML.trim();

                const selections = [];
                for (let q of editors) {
                    if (q.container.id.startsWith("opt")) selections.push(q.root.innerHTML.trim());
                }
                document.getElementById('selectionInput').value = JSON.stringify(selections);

                // 🔹 re-enable tombol setelah 2 detik (fallback kalau AJAX cepat selesai)
                setTimeout(() => {
                    btn.removeAttribute('disabled');
                    btn.innerHTML = originalHTML;
                }, 2000);
            });
        });
    </script>

@endsection

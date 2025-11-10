@extends('layouts.app')

@section('title', 'Tambah Soal')
@section('page_title', 'Tambah Soal')

@section('content')
    <div class="container-fluid">
        <form id="formSoal" action="{{ route('admin.pelajaran.latihan_soal.soal.store', [$lesson_id, $exercise_id]) }}"
            method="POST">
            @csrf
            <input type="hidden" name="selection" id="selectionInput">
            <input type="hidden" id="exercise_id" name="exercise_id" value="{{ $exercise_id }}">
            <input type="hidden" id="exercise_type_id" name="exercise_type_id" value="{{ $exercise_type_id }}">


            <div class="col-lg-12 col-md-12 bg-white p-3 shadow-sm mb-3 rounded">
                <h5 class="fw-bold mb-3 text-uppercase ">Informasi Soal</h5>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Tipe Latihan</label>
                        <input type="text" class="form-control " value="{{ $exerciseType->name }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label for="competence_id" class="form-label fw-semibold">Kompetensi (KD)</label>
                        <select id="competence_id" name="competence_id" class="form-select">
                            <option value="">== Pilih Kompetensi ==</option>
                            @foreach ($competences as $competence)
                                <option value="{{ $competence->id }}">
                                    {{ $competence->point }} - {{ $competence->description }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="exercise_model_id" class="form-label fw-semibold">Model Soal</label>
                        <select id="exercise_model_id" name="exercise_model_id" class="form-select" onchange="loadForm()"
                            required>
                            <option value="">== Pilih ==</option>
                            @foreach ($models as $model)
                                <option value="{{ $model->id }}">{{ $model->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="exercise_choice" class="form-label fw-semibold">Jumlah Pilihan</label>
                        <select id="exercise_choice" name="exercise_choice" class="form-select" onchange="buatPilihan()">
                            <option value="">== Pilih ==</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="6">6</option>
                            <option value="8">8</option>
                            <option value="10">10</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 col-md-12 bg-white p-3 shadow-sm rounded">
                <h5 class="fw-bold mb-3 text-uppercase ">Isi Soal</h5>
                <div id="formArea">
                    <p class="text-muted fst-italic">
                        Pilih model soal terlebih dahulu untuk memulai input...
                    </p>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" id="btnSimpan" class="btn btn-add w-100" disabled>
                        Simpan Soal
                    </button>
                </div>
            </div>
        </form>
    </div>
    {{-- Quill JS --}}
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
@endsection
@section('js')
    <script>
        let editors = [];
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

        function createEditor(id, height = 150) {
            const el = document.getElementById(id);
            if (el) {
                el.style.height = height + "px";
                const q = new Quill(`#${id}`, {
                    theme: "snow",
                    modules: {
                        toolbar
                    },
                    placeholder: "Tulis pertanyaan atau pilihan..."
                });
                editors.push(q);
            }
        }

        function resetEditors() {
            editors = [];
        }

        // 🔹 Buat ulang form sesuai model soal
        function loadForm() {
            const model = document.getElementById("exercise_model_id").value;
            const area = document.getElementById("formArea");
            const choiceSelect = document.getElementById("exercise_choice");
            const btnSimpan = document.getElementById("btnSimpan");

            // enable/disable tombol simpan
            if (model >= 1 && model <= 7) btnSimpan.removeAttribute("disabled");
            else btnSimpan.setAttribute("disabled", true);

            // enable/disable jumlah pilihan
            if (choiceSelect) {
                if (model == "1" || model == "2") choiceSelect.removeAttribute("disabled");
                else choiceSelect.setAttribute("disabled", true);
            }

            area.innerHTML = "";
            resetEditors();

            if (!model) {
                area.innerHTML = "<p><i>Pilih model soal terlebih dahulu...</i></p>";
                return;
            }

            let html = "";

            // 🎯 Model per jenis soal
            switch (model) {
                case "1":
                case "2":
                    html = `
                    <h5>Soal Pilihan Ganda${model == "2" ? " Banyak" : ""}</h5>
                    <label>Pertanyaan:</label>
                    <div id="editorQuestion" class="border p-2 rounded" required></div>
                    <input type="hidden" name="question" id="hiddenQuestion">
                    <div id="pilihanArea" class="mt-3"></div>`;
                    break;
                case "3":
                    html = `
                    <h5>Soal Pernyataan</h5>
                    <label>Pernyataan:</label>
                    <div id="editorQuestion" class="border p-2 rounded" required></div>
                    <input type="hidden" name="question" id="hiddenQuestion">
                    <label class="mt-3">Kunci Jawaban:</label>
                    <select id="answer" name="answer" class="form-select mt-1">
                        <option>Benar</option><option>Salah</option>
                    </select>`;
                    break;
                case "4":
                    html =
                        `
                    <h5>Soal Isian</h5>
                    <label>Pertanyaan:</label>
                    <div id="editorQuestion" class="border p-2 rounded" required></div>
                    <input type="hidden" name="question" id="hiddenQuestion">
                    <label class="mt-3">Kunci Jawaban:</label>
                    <input type="text" id="answer" name="answer" class="form-control" placeholder="Isi jawaban benar" required>`;
                    break;
                case "5":
                    html = `
                    <h5>Soal Uraian</h5>
                    <label>Pertanyaan:</label>
                    <div id="editorQuestion" class="border p-2 rounded" required></div>
                    <input type="hidden" name="question" id="hiddenQuestion">
                    <label class="mt-3">Panduan Penilaian:</label>
                    <div id="editorAnswer" class="border p-2 rounded"></div>
                    <input type="hidden" name="answer" id="hiddenAnswer" required>`;
                    break;
                case "6":
                    html = `
                    <h5>Soal Iya / Tidak</h5>
                    <label>Pertanyaan:</label>
                    <div id="editorQuestion" class="border p-2 rounded" required></div>
                    <input type="hidden" name="question" id="hiddenQuestion">
                    <label class="mt-3">Jawaban:</label>
                    <select id="answer" name="answer" class="form-select mt-1">
                        <option>Iya</option><option>Tidak</option>
                    </select>`;
                    break;
                case "7":
                    html = `
                    <h5>Soal Argumen</h5>
                    <label>Pertanyaan:</label>
                    <div id="editorQuestion" class="border p-2 rounded" required></div>
                    <input type="hidden" name="question" id="hiddenQuestion">
                    <label class="mt-3">Argumen / Jawaban:</label>
                    <div id="editorAnswer" class="border p-2 rounded"></div>
                    <input type="hidden" name="answer" id="hiddenAnswer" required>`;
                    break;
                default:
                    html = `
                    <div class="alert alert-warning text-center p-4 rounded">
                        <h5 class="fw-bold mb-2 text-danger">Model latihan belum dibuat</h5>
                        <p class="mb-0">Silakan buat model latihan untuk tipe ini terlebih dahulu.</p>
                    </div>`;
            }

            area.innerHTML = html;

            // buat editor sesuai model
            if (["1", "2", "3", "4", "5", "6", "7"].includes(model)) {
                createEditor("editorQuestion", 180);
                if (model == "1" || model == "2") buatPilihan();
                if (model == "5" || model == "7") createEditor("editorAnswer", 150);
            }
        }

        // 🔹 Buat pilihan jawaban
        function buatPilihan() {
            const jumlah = parseInt(document.getElementById("exercise_choice").value);
            const model = document.getElementById("exercise_model_id").value;
            const area = document.getElementById("pilihanArea");
            if (!area) return;

            let html = `
            <h6 class="mt-3">Pilihan Jawaban</h6>
            <table class="table table-bordered w-100 table-hover text-center align-middle">
                <thead><tr><th>Abjad</th><th>Pilihan</th></tr></thead>
                <tbody>`;

            for (let i = 0; i < jumlah; i++) {
                const huruf = String.fromCharCode(65 + i);
                html += `
                <tr>
                    <td class="text-center fw-bold">${huruf}</td>
                    <td><div id="opt${huruf}" class="border rounded p-2"></div></td>
                </tr>`;
            }

            html += `</tbody></table><div class="mt-3">`;

            // Jawaban benar
            if (model == "1") {
                html += `<label class="form-label fw-semibold">Jawaban Benar:</label>
                     <select id="answer" name="answer" class="form-select" required>
                     <option value="">-- Pilih Jawaban Benar --</option>`;
                for (let i = 0; i < jumlah; i++) {
                    const huruf = String.fromCharCode(65 + i);
                    html += `<option value="${huruf}">${huruf}</option>`;
                }
                html += `</select>`;
            } else if (model == "2") {
                html += `<label class="form-label fw-semibold">Jawaban Benar:</label>
                     <div class="d-flex flex-wrap gap-2">`;
                for (let i = 0; i < jumlah; i++) {
                    const huruf = String.fromCharCode(65 + i);
                    html += `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="answer[]" value="${huruf}" id="ans${huruf}">
                        <label class="form-check-label" for="ans${huruf}">${huruf}</label>
                    </div>`;
                }
                html += `</div>`;
            }

            html += `</div>`;
            area.innerHTML = html;

            // delay kecil untuk memastikan elemen sudah dirender
            setTimeout(() => {
                for (let i = 0; i < jumlah; i++) {
                    const huruf = String.fromCharCode(65 + i);
                    createEditor(`opt${huruf}`, 100);
                }
            }, 300);
        }

        //   Simpan form soal
        document.getElementById('formSoal').addEventListener('submit', function(e) {
            const btn = document.getElementById("btnSimpan");
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

            const qEditor = editors.find(q => q.container.id === "editorQuestion");
            if (qEditor) {
                const questionHTML = qEditor.root.innerHTML.trim();
                document.getElementById("hiddenQuestion").value = questionHTML;
                if (!questionHTML || questionHTML === "<p><br></p>") {
                    e.preventDefault();
                    alert("Pertanyaan wajib diisi sebelum disimpan!");
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    return false;
                }
            }

            const aEditor = editors.find(q => q.container.id === "editorAnswer");
            if (aEditor && document.getElementById("hiddenAnswer")) {
                document.getElementById("hiddenAnswer").value = aEditor.root.innerHTML.trim();
            }

            const choiceInput = document.getElementById("exercise_choice");
            if (choiceInput && document.getElementById('selectionInput')) {
                const jumlah = parseInt(choiceInput.value);
                const selections = [];
                for (let i = 0; i < jumlah; i++) {
                    const huruf = String.fromCharCode(65 + i);
                    const editor = editors.find(q => q.container.id === `opt${huruf}`);
                    if (editor) selections.push(editor.root.innerHTML.trim());
                }
                document.getElementById('selectionInput').value = JSON.stringify(selections);
            }
        });
    </script>
@endsection

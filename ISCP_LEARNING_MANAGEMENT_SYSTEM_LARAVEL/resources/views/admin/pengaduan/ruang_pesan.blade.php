@extends('layouts.app')

@section('title', 'Ruang Pengadu - Admin')
@section('page_title', 'Ruang Pengadu')

@section('content')

    <div class="card">
        <div class="card-body">

            <h5 class="mb-3">
                Kode Pengaduan:
                <b>{{ $room->complaint_code }}</b>
            </h5>

            {{-- CHAT BOX --}}
            <div id="chatBox"></div>

            {{-- FORM KIRIM --}}
            <form id="sendForm" class="mt-3">
                @csrf
                <input type="hidden" id="roomId" value="{{ $room->id }}">
                <input type="hidden" id="sender"
                    value="Admin({{ auth()->user()->username . '#' . auth()->user()->id }})">

                <div class="input-group">
                    <input type="text" id="msgInput" class="form-control" placeholder="Ketik pesan..." required>
                    <button class="btn btn-primary">Kirim</button>
                </div>
            </form>

        </div>
    </div>

@endsection

@section('js')

    <script>
        window.FirebaseConfig = {
            apiKey: "{{ config('firebase.api_key') }}",
            authDomain: "{{ config('firebase.auth_domain') }}",
            databaseURL: "{{ config('firebase.database_url') }}",
            projectId: "{{ config('firebase.project_id') }}",
            messagingSenderId: "{{ config('firebase.messaging_sender_id') }}",
            appId: "{{ config('firebase.app_id') }}"
        };
    </script>

    <script type="module" src="/js/complaint-realtime.js"></script>

@endsection

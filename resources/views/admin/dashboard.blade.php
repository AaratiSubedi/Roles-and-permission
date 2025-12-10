@extends('layout.app')

@section('title', 'Admin Dashboard')

@section('content')

<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-body">
                <h3>Welcome, {{ auth()->user()->name }} 🎉</h3>
                <p class="text-muted">You are now viewing the Sneat Admin Dashboard.</p>
            </div>
        </div>
    </div>
</div>

@endsection

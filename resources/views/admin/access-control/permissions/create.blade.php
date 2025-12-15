@extends('layout.app')

@section('title', 'Create Permission')

@section('content')
<h4>Create Permission</h4>

<form action="{{ route('admin.permissions.store') }}" method="POST" class="card p-3">
    @csrf
    <div class="form-group mb-3">
    <label for="group">Group / Module</label>
<input type="text" name="group" id="group" class="form-control"
       value="{{ old('group') }}"
       placeholder="Dashboard / Students / Courses / Quizzes">

    </div>
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" value="{{ old('name') }}">
        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Slug (optional)</label>
        <input name="slug" class="form-control" value="{{ old('slug') }}">
        @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Description (optional)</label>
        <textarea name="description" class="form-control">{{ old('description') }}</textarea>
    </div>

    <button class="btn btn-primary">Save</button>
</form>
@endsection
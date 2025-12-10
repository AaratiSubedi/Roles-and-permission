@extends('layout.app')

@section('title', 'Create Role')

@section('content')
    <h4 class="mb-4">Create New Role</h4>
    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label for="name">Role Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="slug">Slug</label>
            <input type="text" name="slug" id="slug" class="form-control">
        </div>
        <div class="form-group mb-3">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Create Role</button>
    </form>
@endsection


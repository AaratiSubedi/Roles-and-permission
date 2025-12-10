@extends('layout.app')
@section('content')
<h2>Edit Permission</h2>
<form method="POST" action="{{ route('admin.permissions.update', $permission) }}">@csrf @method('PUT')
<div class="mb-3">
<label>Name</label>
<input type="text" name="name" value="{{ $permission->name }}" class="form-control">
</div>
<button class="btn btn-success">Update</button>
</form>
@endsection
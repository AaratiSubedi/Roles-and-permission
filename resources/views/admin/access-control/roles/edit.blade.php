@extends('layout.app')


@section('title', 'Edit Role')

@section('content')
<h4>Edit Role</h4>

<form action="{{ route('admin.roles.update', $role) }}" method="POST" class="card p-3 mb-4">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" value="{{ old('name', $role->name) }}">
        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Slug (optional)</label>
        <input name="slug" class="form-control" value="{{ old('slug', $role->slug) }}">
        @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Description (optional)</label>
        <textarea name="description" class="form-control">{{ old('description', $role->description) }}</textarea>
    </div>

<div class="mb-3">
    <label class="form-label">Permissions</label>

    <div class="d-flex gap-3 mb-3">
        <label class="form-check mb-0">
            <input class="form-check-input" type="checkbox" id="selectAllPerms">
            <span class="form-check-label">Select All</span>
        </label>

        <label class="form-check mb-0">
            <input class="form-check-input" type="checkbox" id="deselectAllPerms">
            <span class="form-check-label">Deselect All</span>
        </label>
    </div>

    <div class="row g-3">
        @foreach($permissionsGrouped as $groupName => $perms)
            @php $groupKey = \Illuminate\Support\Str::slug($groupName); @endphp

            <div class="col-md-6">
                <div class="card p-3 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>{{ $groupName }}</strong>

                        <label class="form-check mb-0 small">
                            <input type="checkbox"
                                   class="form-check-input group-toggle"
                                   data-group="{{ $groupKey }}">
                            <span class="form-check-label">Select group</span>
                        </label>
                    </div>

                    <div data-group-box="{{ $groupKey }}">
                        @foreach($perms as $perm)
                            <label class="form-check d-block">
                                <input class="form-check-input perm-item"
                                       type="checkbox"
                                       name="permissions[]"
                                       value="{{ $perm->id }}"
                                       {{ in_array($perm->id, $rolePermissionIds) ? 'checked' : '' }}>
                                <span class="form-check-label">
                                    {{ $perm->name }}
                                    <small class="text-muted">({{ $perm->slug }})</small>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
  const permItems = () => Array.from(document.querySelectorAll(".perm-item"));

  document.getElementById("selectAllPerms")?.addEventListener("change", (e) => {
    if (!e.target.checked) return;
    permItems().forEach(cb => cb.checked = true);
    e.target.checked = false;
  });

  document.getElementById("deselectAllPerms")?.addEventListener("change", (e) => {
    if (!e.target.checked) return;
    permItems().forEach(cb => cb.checked = false);
    e.target.checked = false;
  });

  document.querySelectorAll(".group-toggle").forEach(toggle => {
    toggle.addEventListener("change", (e) => {
      const groupKey = e.target.dataset.group;
      const box = document.querySelector(`[data-group-box="${groupKey}"]`);
      box.querySelectorAll(".perm-item").forEach(cb => cb.checked = e.target.checked);
    });
  });
</script>


    <button class="btn btn-primary">Update</button>
</form>
@endsection

@extends('layout.app')

@section('title', 'Assign Permissions to Role')

@section('content')
    <h4 class="mb-4">Assign Permissions to Role: {{ $role->name }}</h4>

    <div class="card shadow-sm border">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Assign Permissions</h5>

            <div class="d-flex gap-3">
                <label class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" id="selectAllPerms">
                    <span class="form-check-label">Select All</span>
                </label>

                <label class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" id="deselectAllPerms">
                    <span class="form-check-label">Deselect All</span>
                </label>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.roles.updatePermissions', $role->id) }}" method="POST">
                @csrf

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
                                    @foreach($perms as $permission)
                                        <label class="form-check d-block">
                                            <input
                                                type="checkbox"
                                                name="permissions[]"
                                                value="{{ $permission->id }}"
                                                id="permission-{{ $permission->id }}"
                                                class="form-check-input perm-item"
                                                {{ in_array($permission->id, $rolePermissionIds ?? []) ? 'checked' : '' }}
                                            >
                                            <span class="form-check-label" for="permission-{{ $permission->id }}">
                                                {{ $permission->name }}
                                                <small class="text-muted">({{ $permission->slug }})</small>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary mt-3">Save Permissions</button>
            </form>
        </div>

        <div class="card-footer">
            <small class="text-muted">Last updated {{ now()->diffForHumans() }}</small>
        </div>
    </div>
@endsection

@push('scripts')
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
@endpush

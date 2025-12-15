
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Roles List</h4>

    <div class="d-flex align-items-center gap-3">
        {{-- Search --}}
<form method="GET" action="{{ route('admin.access-control.index') }}" class="role-search" id="roleSearchForm">
  <input type="hidden" name="tab" value="roles">
  <div class="input-group role-search-pill">
    <span class="input-group-text bg-white border-0 ps-3">
      <i class="bx bx-search"></i>
    </span>

    <input type="text"
           id="roleSearchInput"
           name="role_q"
           value="{{ request('role_q') }}"
           class="form-control border-0 pe-3"
           placeholder="Search Role...">
  </div>
</form>


        {{-- Add Role --}}
        <button class="btn btn-primary role-add-btn" data-bs-toggle="modal" data-bs-target="#addRoleModal">
            <i class="bx bx-plus me-1"></i> Add Role
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif


    <div class="card">
        <div class="card-body">
            <table class="table table-sm">
        <thead>
            <tr>
                <th style="width:70px;">ID</th>
                <th>Name</th>
                <th>Slug</th>
                {{-- <th style="width:120px;">Users</th> --}}
                <th style="width:140px;">Permissions</th>
                {{-- <th>Description</th> --}}
                <th style="width:120px;">Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>

                    <td>
                        {{-- <span class="text-primary fw-semibold"> --}}
                            {{ $role->name }}
                            {{-- </span> --}}


                        {{-- users count badge --}}
                        <span class="badge bg-info ms-1">
                            {{ $role->users_count ?? 0 }}
                        </span>

                        @if($role->slug === 'super-admin')
                            <span class="badge bg-warning text-dark ms-1">Protected</span>
                        @endif
                    </td>


                    <td>{{ $role->slug }}</td>
                    {{-- <td>{{ $role->description }}</td> --}}
                    <td>
                        <span class="badge bg-primary">
                            {{ $role->permissions_count ?? 0 }}
                        </span>
                    </td>

                    <td>
                        <div class="dropdown">
                            <button class="btn p-0 dropdown-toggle hide-arrow" type="button" id="roleActions{{ $role->id }}"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="roleActions{{ $role->id }}">

                                @if($role->slug !== 'super-admin')
                                    {{-- Edit (Modal) --}}
                                    @php
                                        $roleJson = [
                                            'id' => $role->id,
                                            'name' => $role->name,
                                            'slug' => $role->slug,
                                            'description' => $role->description,
                                            'permission_ids' => $role->permissions
                                                ? $role->permissions->pluck('id')->values()
                                                : [],
                                        ];
                                    @endphp
                                    <li>
                                        <a href="#" class="dropdown-item edit-role-btn" data-role='@json($roleJson)'>
                                            <i class="bx bx-edit-alt me-2"></i> Edit
                                        </a>

                                    </li>

                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>

                                    {{-- Assign Permissions (optional page) --}}
                                        <li>
                                        <a class="dropdown-item" href="{{ route('admin.roles.permissions', $role->id) }}">
                                            <i class="bx bx-key me-2"></i> Assign Permissions
                                        </a>
                                        </li>

                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>

                                    {{-- Delete --}}
                                    <li>
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                            onsubmit="return confirm('Delete this role?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bx bx-trash me-2"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li class="dropdown-item text-muted">
                                        🔒 Protected (Super Admin)
                                    </li>
                                @endif

                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No roles found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    </div>


    {{-- ===================== MODAL: ADD ROLE ===================== --}}
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Create New Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Role Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="Admin, Instructor, Student"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control" placeholder="admin, instructor, student">
                            <small class="text-muted">Leave empty to auto-generate</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" placeholder="Optional"></textarea>
                        </div>

                        {{-- Permissions in Create Modal --}}
                        {{--
                        <hr> --}}
                        <h6 class="mb-2">Assign Permissions</h6>

                        <div class="d-flex gap-3 mb-2">
                            <label class="form-check mb-0">
                                <input type="checkbox" class="form-check-input" id="createSelectAllPerms">
                                <span class="form-check-label">Select All</span>
                            </label>

                            <label class="form-check mb-0">
                                <input type="checkbox" class="form-check-input" id="createDeselectAllPerms">
                                <span class="form-check-label">Deselect All</span>
                            </label>
                        </div>

                        <div class="row g-3">
                            @foreach(($permissionsGrouped ?? []) as $groupName => $perms)
                                @php $groupKey = \Illuminate\Support\Str::slug($groupName); @endphp

                                <div class="col-md-6">
                                    <div class="card p-2 shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong>{{ $groupName }}</strong>

                                            <label class="form-check mb-0 small">
                                                <input type="checkbox" class="form-check-input create-group-toggle"
                                                    data-group="{{ $groupKey }}">
                                                Select group
                                            </label>
                                        </div>

                                        <div data-create-group-box="{{ $groupKey }}">
                                            @foreach($perms as $perm)
                                                <label class="form-check d-block">
                                                    <input class="form-check-input create-perm-item" type="checkbox"
                                                        name="permissions[]" value="{{ $perm->id }}">
                                                    {{ $perm->name }}
                                                    <small class="text-muted">({{ $perm->slug }})</small>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Create Role
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- ===================== MODAL: EDIT ROLE ===================== --}}
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

                <form id="editRoleForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Role Name *</label>
                            <input type="text" name="name" id="editRoleName" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" id="editRoleSlug" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="editRoleDesc" class="form-control"></textarea>
                        </div>

                        <hr>

                        <h6 class="mb-2">Update Permissions</h6>

                        <div class="d-flex gap-3 mb-2">
                            <label class="form-check mb-0">
                                <input type="checkbox" class="form-check-input" id="editSelectAllPerms">
                                <span class="form-check-label">Select All</span>
                            </label>

                            <label class="form-check mb-0">
                                <input type="checkbox" class="form-check-input" id="editDeselectAllPerms">
                                <span class="form-check-label">Deselect All</span>
                            </label>
                        </div>


                        <div class="row g-3">
                            @foreach(($permissionsGrouped ?? []) as $groupName => $perms)
                                <div class="col-md-6">
                                    @php $editGroupKey = \Illuminate\Support\Str::slug($groupName); @endphp

                                    <div class="card p-2 shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong>{{ $groupName }}</strong>

                                            <label class="form-check mb-0 small">
                                                <input type="checkbox" class="form-check-input edit-group-toggle"
                                                    data-group="{{ $editGroupKey }}">
                                                Select group
                                            </label>
                                        </div>

                                        {{-- IMPORTANT wrapper for JS --}}
                                        <div data-edit-group-box="{{ $editGroupKey }}">
                                            @foreach($perms as $perm)
                                                <label class="form-check d-block">
                                                    <input class="form-check-input edit-perm-item" type="checkbox"
                                                        name="permissions[]" value="{{ $perm->id }}">
                                                    {{ $perm->name }}
                                                    <small class="text-muted">({{ $perm->slug }})</small>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Role</button>
                    </div>

                </form>

            </div>
        </div>
    </div>


@push('styles')
<style>
.role-search { width: 360px; }
@media (max-width: 768px){ .role-search { width: 100%; } }
.role-search-pill{
  background:#fff; border:1px solid #d7dde7; border-radius:12px;
  overflow:hidden; height:40px; box-shadow:0 8px 22px rgba(0,0,0,.06);
}
.role-add-btn{ height:40px; border-radius:12px; padding:0 16px; }
</style>
@endpush

@push('scripts')
    <script>
        /* ================= CREATE MODAL: select all + group toggle ================= */
        const createPermItems = () => Array.from(document.querySelectorAll('.create-perm-item'));

        document.getElementById('createSelectAllPerms')?.addEventListener('change', (e) => {
            if (!e.target.checked) return;
            createPermItems().forEach(cb => cb.checked = true);
            e.target.checked = false;
        });

        document.getElementById('createDeselectAllPerms')?.addEventListener('change', (e) => {
            if (!e.target.checked) return;
            createPermItems().forEach(cb => cb.checked = false);
            e.target.checked = false;
        });

        document.querySelectorAll('.create-group-toggle').forEach(toggle => {
            toggle.addEventListener('change', (e) => {
                const groupKey = e.target.dataset.group;
                const box = document.querySelector(`[data-create-group-box="${groupKey}"]`);
                if (!box) return;
                box.querySelectorAll('.create-perm-item').forEach(cb => cb.checked = e.target.checked);
            });
        });

        /* ================= EDIT MODAL: open + populate ================= */
        document.querySelectorAll('.edit-role-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();

                const role = JSON.parse(btn.dataset.role);

                // set form action (adjust if your prefix differs)
                const form = document.getElementById('editRoleForm');
                form.action = `{{ url('/admin/roles') }}/${role.id}`;

                document.getElementById('editRoleName').value = role.name ?? '';
                document.getElementById('editRoleSlug').value = role.slug ?? '';
                document.getElementById('editRoleDesc').value = role.description ?? '';

                const selected = new Set(role.permission_ids || []);
                document.querySelectorAll('.edit-perm-item').forEach(cb => {
                    cb.checked = selected.has(parseInt(cb.value));
                });

                new bootstrap.Modal(document.getElementById('editRoleModal')).show();
            });
        });

        /* ================= EDIT MODAL: select all + group toggle ================= */
        const editPermItems = () => Array.from(document.querySelectorAll('.edit-perm-item'));

        document.getElementById('editSelectAllPerms')?.addEventListener('change', (e) => {
            if (!e.target.checked) return;
            editPermItems().forEach(cb => cb.checked = true);
            e.target.checked = false;
        });

        document.getElementById('editDeselectAllPerms')?.addEventListener('change', (e) => {
            if (!e.target.checked) return;
            editPermItems().forEach(cb => cb.checked = false);
            e.target.checked = false;
        });

        document.querySelectorAll('.edit-group-toggle').forEach(toggle => {
            toggle.addEventListener('change', (e) => {
                const groupKey = e.target.dataset.group;
                const box = document.querySelector(`[data-edit-group-box="${groupKey}"]`);
                if (!box) return;
                box.querySelectorAll('.edit-perm-item').forEach(cb => cb.checked = e.target.checked);
            });
        });

    </script>
    <script>
  function setupLiveSearch(formId, inputId, delay = 350) {
    const form = document.getElementById(formId);
    const input = document.getElementById(inputId);
    if (!form || !input) return;

    let t = null;

    input.addEventListener('input', () => {
      clearTimeout(t);
      t = setTimeout(() => form.submit(), delay);
    });

    // Optional: Enter submits immediately
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        form.submit();
      }
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    setupLiveSearch('roleSearchForm', 'roleSearchInput');
    setupLiveSearch('permSearchForm', 'permSearchInput');
  });
</script>

@endpush
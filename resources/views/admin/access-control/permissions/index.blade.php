
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Permissions</h4>

    <div class="d-flex align-items-center gap-3">
        {{-- Search --}}
<form method="GET" action="{{ route('admin.access-control.index') }}" class="perm-search" id="permSearchForm">
  <input type="hidden" name="tab" value="permissions">
  <div class="input-group perm-search-pill">
    <span class="input-group-text bg-white border-0 ps-3">
      <i class="bx bx-search"></i>
    </span>

    <input type="text"
           id="permSearchInput"
           name="perm_q"
           value="{{ request('perm_q') }}"
           class="form-control border-0 pe-3"
           placeholder="Search Permission...">
  </div>
</form>


        {{-- Add Permission --}}
        <button class="btn btn-primary perm-add-btn" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
            <i class="bx bx-plus me-1"></i> Add Permission
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div id="permAlert"></div>

    <div class="card">
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th style="width:70px;">ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Group</th>
                        <th style="width:140px;">Assigned Roles</th>
                        <th style="width:120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                        @php
                            $permJson = [
                                'id' => $permission->id,
                                'name' => $permission->name,
                                'slug' => $permission->slug,
                                'group' => $permission->group,
                                'description' => $permission->description,
                                'roles_count' => $permission->roles_count ?? 0,
                            ];
                        @endphp

                        <tr id="permRow{{ $permission->id }}">
                            <td>{{ $permission->id }}</td>

                            <td id="permName{{ $permission->id }}">{{ $permission->name }}</td>

                            <td id="permSlug{{ $permission->id }}">{{ $permission->slug }}</td>

                            <td id="permGroup{{ $permission->id }}"><span class="badge bg-info">{{ $permission->group ?? '-' }}
                            </span>
                        </td>

                            <td>
                                <span class="badge bg-primary" id="permRolesCount{{ $permission->id }}">
                                    {{ $permission->roles_count ?? 0 }} Roles
                                </span>
                            </td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn p-0 dropdown-toggle hide-arrow" type="button"
                                            id="permissionActions{{ $permission->id }}"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end"
                                        aria-labelledby="permissionActions{{ $permission->id }}">

                                        <li>
                                            <a href="#"
                                               class="dropdown-item edit-permission-btn"
                                               data-permission='@json($permJson)'>
                                                <i class="bx bx-edit-alt me-2"></i> Edit
                                            </a>
                                        </li>

                                        <li><hr class="dropdown-divider"></li>

                                        <li>
                                            <form action="{{ route('admin.permissions.destroy', $permission->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete this permission?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bx bx-trash me-2"></i> Delete
                                                </button>
                                            </form>
                                        </li>

                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No permissions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $permissions->links() }}
            </div>
        </div>
    </div>

    {{-- ===================== MODAL: BULK ADD PERMISSIONS ===================== --}}
    <div class="modal fade" id="addPermissionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form action="{{ route('admin.permissions.bulkStore') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Add New Permission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Permission Group *</label>
                            <input list="permissionGroupsList"
                                   name="group"
                                   class="form-control"
                                   placeholder="Type or select a group (Dashboard, Courses, Quizzes...)"
                                   required>

                            <datalist id="permissionGroupsList">
                                <option value="Dashboard">
                                <option value="Courses">
                                <option value="Quizzes">
                                <option value="Users">
                                <option value="Roles">
                                <option value="Permissions">
                                <option value="Settings">

                                @foreach(($permissionGroups ?? []) as $group)
                                    <option value="{{ $group }}">
                                @endforeach
                            </datalist>

                            <small class="text-muted">Choose a group (module) like Dashboard, Students, Courses…</small>
                        </div>

                        <label class="form-label">Permissions *</label>

                        <div id="permission-wrapper">
                            <div class="input-group mb-2">
                                <input type="text"
                                       name="permissions[]"
                                       class="form-control"
                                       placeholder="Enter permission name"
                                       required>
                                <button type="button" class="btn btn-success add-permission">+</button>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Permissions</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- ===================== MODAL: EDIT PERMISSION (NO REDIRECT) ===================== --}}
    <div class="modal fade" id="editPermissionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form id="editPermissionForm">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Permission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="editPermId">

                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" id="editPermName" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" id="editPermSlug" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Group</label>
                            <input type="text" id="editPermGroup" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea id="editPermDesc" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="savePermBtn">Save</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

@push('styles')
<style>
.perm-search { width: 360px; }
@media (max-width: 768px){ .perm-search { width: 100%; } }
.perm-search-pill{
  background:#fff; border:1px solid #d7dde7; border-radius:12px;
  overflow:hidden; height:40px; box-shadow:0 8px 22px rgba(0,0,0,.06);
}
.perm-add-btn{ height:40px; border-radius:12px; padding:0 16px; }
</style>
@endpush

@push('scripts')
<script>
  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('add-permission')) {
      const wrapper = document.getElementById('permission-wrapper');
      const div = document.createElement('div');
      div.className = 'input-group mb-2';
      div.innerHTML = `
        <input type="text" name="permissions[]" class="form-control"
               placeholder="Enter permission name" required>
        <button type="button" class="btn btn-danger remove-permission">−</button>
      `;
      wrapper.appendChild(div);
    }

    if (e.target.classList.contains('remove-permission')) {
      e.target.closest('.input-group').remove();
    }
  });

  // Open edit modal
  document.querySelectorAll('.edit-permission-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();

      const perm = JSON.parse(btn.dataset.permission);

      document.getElementById('editPermId').value = perm.id;
      document.getElementById('editPermName').value = perm.name ?? '';
      document.getElementById('editPermSlug').value = perm.slug ?? '';
      document.getElementById('editPermGroup').value = perm.group ?? '';
      document.getElementById('editPermDesc').value = perm.description ?? '';

      new bootstrap.Modal(document.getElementById('editPermissionModal')).show();
    });
  });

  // Save edit via AJAX (no redirect)
  document.getElementById('editPermissionForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const id = document.getElementById('editPermId').value;

    const payload = {
      name: document.getElementById('editPermName').value,
      slug: document.getElementById('editPermSlug').value,
      group: document.getElementById('editPermGroup').value,
      description: document.getElementById('editPermDesc').value,
      _token: '{{ csrf_token() }}',
      _method: 'PUT'
    };

    const res = await fetch(`{{ url('/admin/permissions') }}/${id}/ajax-update`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(payload)
    });

    if (!res.ok) {
      let msg = 'Failed to update permission.';
      try {
        const data = await res.json();
        msg = data.message || msg;
      } catch(e) {}
      showPermAlert('danger', msg);
      return;
    }

    const data = await res.json();
    if (!data.success) {
      showPermAlert('danger', data.message || 'Failed to update.');
      return;
    }

    // Update row UI
    document.getElementById(`permName${id}`).innerText = data.permission.name ?? '';
    document.getElementById(`permSlug${id}`).innerText = data.permission.slug ?? '';
    document.getElementById(`permGroup${id}`).innerHTML =
  `<span class="badge bg-info">${data.permission.group ?? '-'}</span>`;

    document.getElementById(`permRolesCount${id}`).innerText = `${data.permission.roles_count ?? 0} Roles`;

    bootstrap.Modal.getInstance(document.getElementById('editPermissionModal')).hide();
    showPermAlert('success', data.message || 'Updated!');
  });

  function showPermAlert(type, msg) {
    const el = document.getElementById('permAlert');
    el.innerHTML = `<div class="alert alert-${type}">${msg}</div>`;
    setTimeout(() => el.innerHTML = '', 2500);
  }
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

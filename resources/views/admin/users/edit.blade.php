@extends('layout.app')

@section('title', 'Edit User Access')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Edit Access</h4>
        <small class="text-muted">{{ $user->name }} ({{ $user->email }})</small>
    </div>

    <a href="{{ route('admin.users.index') }}" class="btn btn-light">
        ← Back
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@php
    // Group the not-assigned permissions by group column
    $permissionsGrouped = collect($permissionsNotAssigned)->groupBy(function($p){
        return $p->group ?: 'Other';
    });
@endphp

<div class="row g-3">

    {{-- Roles --}}
    <div class="col-lg-5">
        <div class="card shadow-sm h-40">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong>Roles</strong>
                    <div class="small text-muted">Assign roles to the user</div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="rolesSelectAll">Select all</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="rolesDeselectAll">Clear</button>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.users.roles.update', $user) }}" method="POST">
                    @csrf

                    <div class="row">
                        @foreach($roles as $role)
                            <div class="col-12">
                                <label class="d-flex align-items-center gap-2 py-1">
                                    <input class="form-check-input role-checkbox"
                                           type="checkbox"
                                           name="roles[]"
                                           value="{{ $role->id }}"
                                           {{ in_array($role->id, $userRoleIds) ? 'checked' : '' }}>
                                    <span>
                                        <strong>{{ $role->name }}</strong>
                                        <small class="text-muted">({{ $role->slug }})</small>
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <button class="btn btn-primary mt-3">Save Roles</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Direct permissions overrides --}}
    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong>Direct Permissions (Overrides)</strong>
                    <div class="small text-muted">
                        Apply user-specific overrides for permissions that are not provided by roles.
                        <span class="ms-1 text-muted">Allow/Deny overrides role permissions.</span>
                    </div>
                </div>

                <div class="d-flex gap-2 flex-wrap justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-success" id="allowAll">Allow all</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="denyAll">Deny all</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAll">Clear all</button>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.users.permissions.update', $user) }}" method="POST" id="directPermForm">
                    @csrf

                    @if($permissionsGrouped->count() === 0)
                        <div class="text-muted">No direct permissions available.</div>
                    @else

                        @foreach($permissionsGrouped as $groupName => $perms)
                            @php
                                $groupKey = \Illuminate\Support\Str::slug($groupName);
                            @endphp

                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong>{{ $groupName }}</strong>
                                        <span class="badge bg-light text-dark ms-2">{{ $perms->count() }}</span>
                                    </div>

                                    <div class="d-flex gap-2 flex-wrap">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-success group-allow"
                                                data-group="{{ $groupKey }}">
                                            Allow group
                                        </button>

                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger group-deny"
                                                data-group="{{ $groupKey }}">
                                            Deny group
                                        </button>

                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary group-clear"
                                                data-group="{{ $groupKey }}">
                                            Clear group
                                        </button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr class="text-muted">
                                                <th>Permission</th>
                                                <th style="width:170px;">Override</th>
                                            </tr>
                                        </thead>
                                        <tbody data-group-box="{{ $groupKey }}">
                                        @foreach($perms as $perm)
                                            @php
                                                $current = $userDirectPerms[$perm->id] ?? null;
                                                $currentType = $current?->pivot?->type; // allow|deny|null
                                            @endphp

                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $perm->name }}</div>
                                                    <div class="small text-muted">{{ $perm->slug }}</div>
                                                </td>

                                                <td>
                                                    {{-- Hidden input used by backend --}}
                                                    <input type="hidden"
                                                           name="types[{{ $perm->id }}]"
                                                           class="perm-hidden"
                                                           data-perm="{{ $perm->id }}"
                                                           value="{{ $currentType ?? '' }}">

                                                    <div class="d-flex gap-3">
                                                        <label class="form-check mb-0">
                                                            <input type="checkbox"
                                                                   class="form-check-input perm-allow"
                                                                   data-perm="{{ $perm->id }}"
                                                                   {{ $currentType === 'allow' ? 'checked' : '' }}>
                                                            <span class="form-check-label">Allow</span>
                                                        </label>

                                                        <label class="form-check mb-0">
                                                            <input type="checkbox"
                                                                   class="form-check-input perm-deny"
                                                                   data-perm="{{ $perm->id }}"
                                                                   {{ $currentType === 'deny' ? 'checked' : '' }}>
                                                            <span class="form-check-label">Deny</span>
                                                        </label>

                                                        <button type="button"
                                                                class="btn btn-sm btn-light ms-auto perm-clear"
                                                                data-perm="{{ $perm->id }}">
                                                            Clear
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <button class="btn btn-primary mt-2">Save Direct Permissions</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
/* ===================== ROLES select all/clear ===================== */
document.getElementById('rolesSelectAll')?.addEventListener('click', () => {
  document.querySelectorAll('.role-checkbox').forEach(cb => cb.checked = true);
});
document.getElementById('rolesDeselectAll')?.addEventListener('click', () => {
  document.querySelectorAll('.role-checkbox').forEach(cb => cb.checked = false);
});

/* ===================== DIRECT PERMS: allow/deny mutual exclusive ===================== */
function setPermValue(permId, val) {
  const hidden = document.querySelector(`.perm-hidden[data-perm="${permId}"]`);
  const allowCb = document.querySelector(`.perm-allow[data-perm="${permId}"]`);
  const denyCb  = document.querySelector(`.perm-deny[data-perm="${permId}"]`);

  if (!hidden || !allowCb || !denyCb) return;

  if (val === 'allow') {
    allowCb.checked = true;
    denyCb.checked = false;
    hidden.value = 'allow';
  } else if (val === 'deny') {
    denyCb.checked = true;
    allowCb.checked = false;
    hidden.value = 'deny';
  } else {
    allowCb.checked = false;
    denyCb.checked = false;
    hidden.value = '';
  }
}

document.addEventListener('change', (e) => {
  if (e.target.classList.contains('perm-allow')) {
    const permId = e.target.dataset.perm;
    setPermValue(permId, e.target.checked ? 'allow' : '');
  }

  if (e.target.classList.contains('perm-deny')) {
    const permId = e.target.dataset.perm;
    setPermValue(permId, e.target.checked ? 'deny' : '');
  }
});

document.addEventListener('click', (e) => {
  if (e.target.classList.contains('perm-clear')) {
    const permId = e.target.dataset.perm;
    setPermValue(permId, '');
  }
});

/* ===================== GLOBAL allow/deny/clear ===================== */
document.getElementById('allowAll')?.addEventListener('click', () => {
  document.querySelectorAll('.perm-hidden').forEach(h => setPermValue(h.dataset.perm, 'allow'));
});
document.getElementById('denyAll')?.addEventListener('click', () => {
  document.querySelectorAll('.perm-hidden').forEach(h => setPermValue(h.dataset.perm, 'deny'));
});
document.getElementById('clearAll')?.addEventListener('click', () => {
  document.querySelectorAll('.perm-hidden').forEach(h => setPermValue(h.dataset.perm, ''));
});

/* ===================== GROUP allow/deny/clear ===================== */
function setGroup(groupKey, val) {
  const box = document.querySelector(`[data-group-box="${groupKey}"]`);
  if (!box) return;

  box.querySelectorAll('.perm-hidden').forEach(h => {
    setPermValue(h.dataset.perm, val);
  });
}

document.querySelectorAll('.group-allow').forEach(btn => {
  btn.addEventListener('click', () => setGroup(btn.dataset.group, 'allow'));
});
document.querySelectorAll('.group-deny').forEach(btn => {
  btn.addEventListener('click', () => setGroup(btn.dataset.group, 'deny'));
});
document.querySelectorAll('.group-clear').forEach(btn => {
  btn.addEventListener('click', () => setGroup(btn.dataset.group, ''));
});
</script>
@endpush

@extends('layout.app')
@section('title', 'Roles & Permissions')

@php
  $tab = request('tab', 'roles'); // roles | permissions
@endphp

@section('content')

<div class="ac-page">

  {{-- Top header + tabs (compact) --}}
  <div class="ac-top">
    <div class="ac-title">
      <h3 class="mb-1">Roles &amp; Permissions</h3>
      <p class="text-muted mb-0">Manage user roles and their respective permissions.</p>
    </div>

    <ul class="nav nav-tabs ac-tabs" role="tablist">
      <li class="nav-item">
        <button class="nav-link {{ $tab === 'roles' ? 'active' : '' }}"
                id="tab-roles-btn"
                data-bs-toggle="tab"
                data-bs-target="#tab-roles"
                type="button" role="tab">
          <i class="bx bx-crown me-1"></i> Roles
        </button>
      </li>

      <li class="nav-item">
        <button class="nav-link {{ $tab === 'permissions' ? 'active' : '' }}"
                id="tab-perms-btn"
                data-bs-toggle="tab"
                data-bs-target="#tab-perms"
                type="button" role="tab">
          <i class="bx bx-key me-1"></i> Permissions
        </button>
      </li>
    </ul>
  </div>

  {{-- Single surface card (removes “empty page feel”) --}}
  <div class="ac-surface">
    <div class="tab-content">
      <div class="tab-pane fade {{ $tab === 'roles' ? 'show active' : '' }}" id="tab-roles" role="tabpanel">
        @include('admin.access-control.roles.index')
      </div>

      <div class="tab-pane fade {{ $tab === 'permissions' ? 'show active' : '' }}" id="tab-perms" role="tabpanel">
        @include('admin.access-control.permissions.index')
      </div>
    </div>
  </div>

</div>
@endsection

@push('styles')
<style>
  .ac-page { max-width: 1220px; }

  .ac-top{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:16px;
    margin-bottom:14px;
  }
  @media (max-width: 768px){
    .ac-top{ flex-direction:column; align-items:stretch; }
  }

  .ac-title h3{ font-weight:700; letter-spacing:-.02em; }

  /* pill tabs */
  .ac-tabs{
    border-bottom:0;
    background:#f2f4f8;
    padding:6px;
    border-radius:14px;
    gap:6px;
    flex-wrap:nowrap;
  }
  .ac-tabs .nav-link{
    border:0 !important;
    border-radius:12px;
    padding:10px 14px;
    font-weight:600;
    color:#667085;
    background:transparent;
    white-space:nowrap;
  }
  .ac-tabs .nav-link.active{
    background:#fff;
    color:#1f2937;
    box-shadow:0 10px 24px rgba(0,0,0,.08);
  }

  .ac-surface{
    background:#fff;
    border:1px solid #e6eaf2;
    border-radius:18px;
    box-shadow:0 12px 32px rgba(0,0,0,.06);
    overflow:hidden;
  }

  /* reduce inner whitespace */
  .ac-surface .tab-pane{ padding:16px; }
  @media (max-width: 768px){
    .ac-surface .tab-pane{ padding:12px; }
  }
</style>
@endpush

@push('scripts')
<script>
  // keep URL updated when user clicks tabs (so refresh/search keeps same tab)
  document.addEventListener('DOMContentLoaded', () => {
    const rolesBtn = document.getElementById('tab-roles-btn');
    const permsBtn = document.getElementById('tab-perms-btn');

    function setTabParam(value){
      const url = new URL(window.location.href);
      url.searchParams.set('tab', value);
      window.history.replaceState({}, '', url);
    }

    rolesBtn?.addEventListener('shown.bs.tab', () => setTabParam('roles'));
    permsBtn?.addEventListener('shown.bs.tab', () => setTabParam('permissions'));
  });

  // live search helper
  function setupLiveSearch(formId, inputId, delay = 350) {
    const form = document.getElementById(formId);
    const input = document.getElementById(inputId);
    if (!form || !input) return;

    let t = null;
    input.addEventListener('input', () => {
      clearTimeout(t);
      t = setTimeout(() => form.submit(), delay);
    });

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

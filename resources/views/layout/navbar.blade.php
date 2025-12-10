<nav class="layout-navbar navbar navbar-expand-xl navbar-light bg-white">
    <div class="container-fluid">
        <span class="navbar-brand">Admin Panel</span>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-danger btn-sm">Logout</button>
                </form>
            </li>
        </ul>
    </div>
</nav>

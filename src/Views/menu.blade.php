<!-- - - - - - - - - - - - - - - - - - - - - - - - - -->
<!-- Menu -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - - -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">

    <div class="container-fluid">

        <a class="navbar-brand" href="{{ route('backend-home') }}">
            <div class="logo">
                Backend
            </div>
        </a>
        
        <button type="button"
            class="navbar-toggler"
            data-toggle="collapse" 
            data-target="#navbar-menu" 
            aria-controls="navbar-menu" 
            aria-expanded="false" 
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="navbar-nav ml-auto" style="font-size: 1.5rem">

                <li class="nav-item {{ Request::route()->getName() === 'backend-media' ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('backend-media') }}">
                        <i class="fas fa-cog"></i>
                    </a>
                </li>

                <li class="nav-item {{ Request::route()->getName() === 'backend-media' ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('backend-media') }}">
                        <i class="fas fa-sitemap"></i>
                    </a>
                </li>

                <li class="nav-item {{ Request::route()->getName() === 'backend-media' ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('backend-media') }}">
                        <i class="fas fa-file-image"></i>
                    </a>
                </li>

                <li class="nav-item {{ Request::route()->getName() === 'backend-media' ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('backend-media') }}">
                        <i class="fas fa-user-secret"></i>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}" target="_blank">
                        <i class="fas fa-globe"></i>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <i class="fas fa-power-off"></i>
                    </a>
                    <form id="logout-form" action="{{ route('admin-logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </li>
            </ul>
        </div>

    </div>

</nav>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{ asset('assets/images/faces/face1.jpg')}}" alt="profile" />
          <span class="login-status online"></span>
          <!--change to offline or busy as needed-->
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">{{ Auth::user()->name }}</span>
          <span class="text-secondary text-small">Project Manager</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('dashboard') }}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>

    <li class="nav-item {{ request()->routeIs('kategori') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('kategori.create') }}">
        <span class="menu-title">Kategori</span>
        <i class="mdi mdi-format-list-bulleted menu-icon"></i>
      </a>
    </li>

    <li class="nav-item {{ request()->routeIs('buku') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('buku.create') }}">
        <span class="menu-title">Buku</span>
        <i class="mdi mdi-book-multiple menu-icon"></i>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#menu-pdf"
        aria-expanded="{{ request()->routeIs('generate.pdf.*') ? 'true' : 'false' }}"
        aria-controls="menu-pdf">
        <span class="menu-title">PDF</span>
        <i class="menu-arrow"></i> <i class="mdi mdi-file-pdf-box menu-icon"></i>
      </a>

      <div class="collapse {{ request()->routeIs('generate.pdf.*') ? 'show' : '' }}" id="menu-pdf">
        <ul class="nav flex-column sub-menu">

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('generate.pdf.landscape') ? 'active' : '' }}"
              href="{{ route('generate.pdf.landscape') }}">
              Landscape
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('generate.pdf.portrait') ? 'active' : '' }}"
              href="{{ route('generate.pdf.portrait') }}">
              Portrait
            </a>
          </li>

        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#menu-barang"
        aria-expanded="{{ request()->routeIs('barang.*') ? 'true' : 'false' }}"
        aria-controls="menu-barang">
        <span class="menu-title">Data Barang</span>
        <i class="menu-arrow"></i> <i class="mdi mdi-package-variant menu-icon"></i>
      </a>

      <div class="collapse {{ request()->routeIs('barang.*') ? 'show' : '' }}" id="menu-barang">
        <ul class="nav flex-column sub-menu">

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('barang.index') ? 'active' : '' }}"
              href="{{ route('barang.index') }}">
              Data Barang
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('barang.create') ? 'active' : '' }}"
              href="{{ route('barang.create') }}">
              Buat Label Barang
            </a>
          </li>

          <!--<li class="nav-item">
                <a class="nav-link {{ request()->routeIs('barang.cetak') ? 'active' : '' }}"
                    href="{{ route('barang.cetak') }}">
                    Cetak PDF Label Barang
                </a>
            </li>-->

        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="https://github.com/naufalbagaskaraaaa/WPL_1" target="_blank">
        <span class="menu-title">Dokumentasi</span>
        <i class="mdi mdi-github menu-icon"></i>
      </a>
    </li>
  </ul>
</nav>
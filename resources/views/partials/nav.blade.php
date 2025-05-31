    <!-- ======== sidebar-nav start =========== -->
    <aside class="sidebar-nav-wrapper">
      <div class="navbar-logo">
        <a href="index.html">
          <!-- <img src="assets/images/logo/logo.svg" alt="logo" /> -->
          <h2 class="text-primary">NgaturUang</h2>
        </a>
      </div>
      <nav class="sidebar-nav">
        <ul>
          <li class="nav-item {{ request()->is('home') ? 'active' : '' }}">
            <a href="{{ route('home') }}">
              <span class="icon">
                <span class="mdi mdi-home-outline mdi-24px"></span>
              </span>
              <span class="text">Halaman Utama</span>
            </a>
          </li>
          <li class="nav-item {{ request()->is('wallet*') ? 'active' : '' }}">
            <a href="{{ route('wallet') }}">
              <span class="icon">
              <span class="mdi mdi-wallet-outline mdi-24px"></span>
              </span>
              <span class="text">Dompet & Rekening</span>
            </a>
          </li>
          <li class="nav-item nav-item-has-children">
            <a
              href="#0"
              class="{{ request()->is('income*', 'transaction*') ? '' : 'collapsed' }}"
              data-bs-toggle="collapse"
              data-bs-target="#ddmenu_1"
              aria-controls="ddmenu_1"
              aria-expanded="false"
              aria-label="Toggle navigation"
            >
              <span class="icon">
                <span class="mdi mdi-swap-vertical mdi-24px"></span>
              </span>
              <span class="text">Transaksi</span>
            </a>
            <ul id="ddmenu_1" class="collapse {{ request()->is('transaction*') ? 'show' : '' }} dropdown-nav">
              <li>
                <a href="{{ route('category') }}" class="{{ request()->is('transaction/category*') ? 'active' : '' }} ms-1"> Kategori </a>
              </li>
              <li>
                <a href="{{ route('income') }}" class="{{ request()->is('transaction/income*') ? 'active' : '' }} ms-1"> Pemasukan </a>
              </li>
              <li>
                <a href="{{ route('expense') }}" class="{{ request()->is('transaction/expense*') ? 'active' : '' }} ms-1"> Pengeluaran </a>
              </li>
            </ul>
          </li>
          <li class="nav-item {{ request()->is('budget*') ? 'active' : '' }}">
            <a href="{{ route('budget') }}">
              <span class="icon">
              <span class="mdi mdi-wallet-outline mdi-24px"></span>
              </span>
              <span class="text">Anggaran</span>
            </a>
          </li>
          
        </ul>
      </nav>
    </aside>
    <div class="overlay"></div>
    <!-- ======== sidebar-nav end =========== -->
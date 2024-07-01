 <!-- Menu -->

 <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
      <a href="index.html" class="app-brand-link">
        RFID GURU
        {{-- <img src="{{ asset('assets/img/logo.png') }}" alt="" srcset="" style="width: 100%;"> --}}
      </a>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
        <i class="bx bx-chevron-left bx-sm align-middle"></i>
      </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
      <li class="menu-item active">
        <a href="{{ route('administrator') }}" class="menu-link">
          <i class="menu-icon tf-icons bx bx-home-circle"></i>
          <div data-i18n="Analytics">Dashboard</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="{{ route('guru') }}" class="menu-link">
          <i class="menu-icon tf-icons bx bx-cart-alt"></i>
          <div data-i18n="Analytics">Guru</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="{{ route('kelas') }}" class="menu-link">
          <i class="menu-icon tf-icons bx bx-briefcase"></i>
          <div data-i18n="Analytics">Kelas</div>
        </a>
      </li>
    </ul>
  </aside>
  <!-- / Menu -->
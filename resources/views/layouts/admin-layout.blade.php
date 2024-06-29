<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="{{ asset('AdminLTE') }}/img/logo/logo pnc.png" rel="icon">
  <title>Super Admin - Dashboard</title>
  <link href="{{ asset('AdminLTE') }}/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="{{ asset('AdminLTE') }}/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="{{ asset('AdminLTE') }}/css/ruang-admin.min.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
</head>

<body id="page-top">
    <div id="wrapper">
      <!-- Sidebar -->
      <ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
          <div class="sidebar-brand-icon">
            <img src="{{ asset('AdminLTE') }}/img/logo/logo pnc.png">
          </div>
          <div class="sidebar-brand-text mx-3">SIPROKER</div>
        </a>
        <hr class="sidebar-divider my-0">
        <li class="nav-item active">
          <a class="nav-link" href="{{ url('/dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
        </li>
        <hr class="sidebar-divider">
        <div class="sidebar-heading">
          Menu
        </div>
        <li class="nav-item">
          <a class="nav-link" href="{{ url('/organisasi') }}">
            <i class="fas fa-fw fa-sitemap"></i>
            <span>Organisasi</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ url('/usermanajemen') }}">
            <i class="fas fa-fw fa-user-circle"></i>
            <span>Akun</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ url('/lihatproposal') }}">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Proposal</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ url('/lihatlpj') }}">
            <i class="fas fa-fw fa-file-invoice"></i>
            <span>LPJ</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ url('/laporan/proker') }}">
            <i class="fas fa-fw fa-clipboard-check"></i>
            <span>Laporan Program Kerja</span>
          </a>
        </li>
        <hr class="sidebar-divider">
        <div class="version" id="version-ruangadmin"></div>
      </ul>
      <!-- Sidebar -->
      <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
          <!-- TopBar -->
          <nav class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top">
            <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
              <i class="fa fa-bars"></i>
            </button>
            <ul class="navbar-nav ml-auto">
              <div class="topbar-divider d-none d-sm-block"></div>
              <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
                  <img class="img-profile rounded-circle" src="{{ asset('AdminLTE') }}/img/boy.png" style="max-width: 60px">
                  <span class="ml-2 d-none d-lg-inline text-white text-large">
                    @if(Auth::user()->role == '1')
                        Admin : {{ Auth::user()->name }}
                    @elseif(Auth::user()->role == '2')
                        Sekretaris : {{ Auth::user()->name }}
                    @elseif(Auth::user()->role == '3')
                        Pengecek : {{ Auth::user()->name }}
                    @else
                       {{ Auth::user()->name }}
                    @endif
                </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                  <a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                  </a>
                </div>
              </li>
            </ul>
          </nav>
          <!-- Topbar -->
  
          <!-- Container Fluid-->
          <div class="container-fluid" id="container-wrapper">
            @yield('kontainer')
          </div>
          <!---Container Fluid-->
        </div>
        <!-- Footer -->
        <footer class="sticky-footer bg-white">
          <div class="container my-auto">
            <div class="copyright text-center my-auto">
              <span>copyright &copy; <script> document.write(new Date().getFullYear()); </script> - developed by
                <b><a>Riska Retno Larasati</a></b>
              </span>
            </div>
          </div>
        </footer>
        <!-- Footer -->
      </div>
    </div>
  
    <!-- Scroll to top -->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
    </a>

    <!-- Modal Logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Logout</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <a href="{{ route('logout') }}" class="btn btn-danger"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal Logout -->
  
    <script src="{{ asset('AdminLTE') }}/vendor/jquery/jquery.min.js"></script>
    <script src="{{ asset('AdminLTE') }}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('AdminLTE') }}/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="{{ asset('AdminLTE') }}/js/ruang-admin.min.js"></script>
    <script src="{{ asset('AdminLTE') }}/vendor/chart.js/Chart.min.js"></script>
    <script src="{{ asset('AdminLTE') }}/js/demo/chart-area-demo.js"></script>  
    <script>
      // Fungsi untuk membuka modal logout
      function openLogoutModal() {
          $('#logoutModal').modal('show'); // Menampilkan modal
      }
  </script>
  <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
  <script>
      $(document).ready( function (){
        $('#myDataTable').DataTable();
      });
  </script>
  @yield('script')
  </body>
  
  </html>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>@yield('title') - {{$setting->title}}</title>

        <!-- Custom fonts for this template-->
        <link href="{{asset('assets/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
            <!-- Custom styles for this template-->
            <link href="{{asset('assets/css/sb-admin-2.min.css')}}" rel="stylesheet">
        @yield('css')
        </head>
        <body id="page-top">
            <!-- Page Wrapper -->
            <div id="wrapper">
                <!-- Sidebar -->
                    @include('admin.partials._menu-sidebar')
                    <!-- End of Sidebar -->
                    <!-- Content Wrapper -->
                    <div id="content-wrapper" class="d-flex flex-column">
                        <!-- Main Content -->
                        <div id="content">
                            @include('admin.partials._menu-profile')
                            <!-- Begin Page Content -->
                            @yield('content')
                            <!-- /.container-fluid -->
                        </div>
                        <!-- End of Main Content -->
                        <!-- Footer -->
                        <footer class="sticky-footer bg-white">
                            <div class="container my-auto">
                                <div class="copyright text-center my-auto">
                                    <span>Copyright &copy; Stok Barang {{$setting->title}} 2023</span>
                                </div>
                            </div>
                        </footer>
                        <!-- End of Footer -->
                    </div>
                    <!-- End of Content Wrapper -->
                </div>
                <!-- End of Page Wrapper -->
                <!-- Scroll to Top Button-->
                <a class="scroll-to-top rounded" href="#page-top">
                    <i class="fas fa-angle-up"></i>
                </a>
                @include('admin.partials._logout-modal')
                <!-- Bootstrap core JavaScript-->
                <script src="{{asset('assets/vendor/jquery/jquery.min.js')}}"></script>
                <script src="{{asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
                <!-- Core plugin JavaScript-->
                <script src="{{asset('assets/vendor/jquery-easing/jquery.easing.min.js')}}"></script>
                <!-- Custom scripts for all pages-->
                <script src="{{asset('assets/js/sb-admin-2.min.js')}}"></script>
                <script type="text/javascript">
                    $('div.alert-info').not('.alert-secondary').delay(2000).slideUp(300);
                </script>
                @yield('js')
            </body>
        </html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OTP Verification</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth">
                <div class="row flex-grow">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left p-5">
                            <div class="brand-logo">
                                <img src="{{ asset('assets/images/logo.svg') }}">
                            </div>

                            <h4>OTP Verification</h4>
                            <h6 class="font-weight-light">Masukkan 6 digit kode OTP yang dikirim ke emailmu</h6>

                            @if(session('error'))
                            <div class="alert alert-danger p-2 mt-3" style="font-size: 14px;">
                                {{ session('error') }}
                            </div>
                            @endif

                            <form class="pt-3" method="POST" action="{{ route('otp.process') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-lg text-center text-uppercase"
                                        id="otp" name="otp" maxlength="6"
                                        style="font-size: 28px; letter-spacing: 12px; font-weight: bold; border: 2px solid #b66dff; border-radius: 10px;"
                                        placeholder="••••••" required autofocus autocomplete="off">
                                </div>

                                <div class="mt-3 d-grid gap-2">
                                    <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                                        VERIFIKASI OTP
                                    </button>
                                </div>

                                <div class="text-center mt-4 font-weight-light">
                                    Tidak menerima email? <a href="#" class="text-primary">Kirim ulang</a>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>
</body>

</html>
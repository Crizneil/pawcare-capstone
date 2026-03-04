<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="wpOceans">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/newicon.png') }}">
    <title>Paw Care | Pet Care & Veterinary HTML5 Template</title>
    <link href="assets/css/themify-icons.css" rel="stylesheet">
    <link href="assets/css/flaticon.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/animate.css" rel="stylesheet">
    <link href="assets/css/owl.carousel.css" rel="stylesheet">
    <link href="assets/css/owl.theme.css" rel="stylesheet">
    <link href="assets/css/slick.css" rel="stylesheet">
    <link href="assets/css/slick-theme.css" rel="stylesheet">
    <link href="assets/css/swiper.min.css" rel="stylesheet">
    <link href="assets/css/owl.transitions.css" rel="stylesheet">
    <link href="assets/css/jquery.fancybox.css" rel="stylesheet">
    <link href="assets/css/odometer-theme-default.css" rel="stylesheet">
    <link href="assets/sass/style.css" rel="stylesheet">
</head>

<body>

    <!-- start page-wrapper -->
    <div class="page-wrapper">
        <!-- start preloader -->
        <div class="preloader">
            <div class="vertical-centered-box">
                <div class="content">
                    <div class="loader-circle"></div>
                    <div class="loader-line-mask">
                        <div class="loader-line"></div>
                    </div>
                    <img src="assets/images/newicon.png" alt="">
                </div>
            </div>
        </div>
        <!-- end preloader -->

        <!-- Start header -->
        <header id="header">
            <div class="wpo-site-header">
                <nav class="navigation navbar navbar-expand-lg navbar-light">
                    <div class="container-fluid">
                        <div class="row align-items-center">

                            <div class="col-lg-3 col-md-3 col-3 d-lg-none dl-block order-1">
                                <div class="mobail-menu">
                                    <button type="button" class="navbar-toggler open-btn">
                                        <span class="sr-only">Toggle navigation</span>
                                        <span class="icon-bar first-angle"></span>
                                        <span class="icon-bar middle-angle"></span>
                                        <span class="icon-bar last-angle"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-4 col-4 order-2">
                                <div class="navbar-header">
                                    <a class="navbar-brand" href="{{ url('/') }}">
                                        <img src="assets/images/newlogo.png" alt="Logo">
                                    </a>
                                </div>
                            </div>

                            <div class="col-lg-7 col-md-1 col-1 order-lg-3">
                                <div id="navbar" class="collapse navbar-collapse navigation-holder">
                                    <button class="menu-close"><i class="ti-close"></i></button>
                                    <ul class="nav navbar-nav mb-2 mb-lg-0">
                                        <li class="menu-item-has-children"><a href="index.html">Home</a></li>
                                        <li class="menu-item-has-children">
                                            <a href="#">Page</a>
                                            <ul class="sub-menu">
                                                <li><a href="about.html">About</a></li>
                                                <li><a href="faq.html">Faq</a></li>
                                                <li><a href="contact.html">Contact Us</a></li>
                                            </ul>
                                        </li>
                                        <li class="menu-item-has-children">
                                            <a href="#">Services</a>
                                            <ul class="sub-menu">
                                                <li><a href="service.html">Services</a></li>
                                                <li><a href="service-single.html">Service Single</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="blog.html">Blog</a></li>

                                        <li class="d-lg-none"><a href="{{ route('login') }}">Login</a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-4 order-3 order-lg-4">
                                <div class="header-right" style="display: flex; align-items: center; justify-content: flex-end; gap: 15px;">
                                    <div class="header-search-form-wrapper">
                                    </div>

                                    <div class="header-login-btn d-none d-lg-block">
                                        <a class="custom-login-btn" href="{{ route('login') }}">Login</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </nav>
            </div>
        </header>
        <!-- end of header -->

        <!-- start wpo-page-title -->
        <section class="wpo-page-title">
            <div class="container">
                <div class="row">
                    <div class="col col-xs-12">
                        <div class="wpo-breadcumb-wrap">
                            <h2>Service Single</h2>
                            <ol class="wpo-breadcumb-wrap">
                                <li><a href="index.html">Home</a></li>
                                <li>Service</li>
                            </ol>
                        </div>
                    </div>
                </div> <!-- end row -->
            </div> <!-- end container -->
        </section>
        <!-- end page-title -->

        <!-- service-single-area start -->
        <div class="wpo-service-single-area section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-12">
                        <div class="wpo-service-single-wrap">
                            <div class="wpo-service-single-item">
                                <div class="wpo-service-single-main-img">
                                    <img src="assets/images/service-single/1.jpg" alt="">
                                </div>
                                <div class="wpo-service-single-title">
                                    <h3>Pet Vaccination &amp; Health Monitoring</h3>
                                </div>
                                <p class="service-single-subtitle">Smart, Secure, and Reliable Preventive Care</p>
                                <p>PawCare’s Pet Vaccination and Health Monitoring service is designed to help veterinary
                                    clinics manage pet health records efficiently while ensuring pets receive timely and
                                    proper care.</p>
                                <p>This service supports preventive veterinary care through organized digital records and
                                    easy access to essential information.</p>
                            </div>

                            <div class="wpo-service-single-item">
                                <div class="wpo-service-single-title">
                                    <h3>What This Service Does</h3>
                                </div>
                                <p>This service digitally records and tracks pet vaccination schedules and health
                                    history. By using a simple scanning method, authorized veterinary staff can instantly
                                    access a pet’s records during clinic visits, reducing delays and improving accuracy.</p>
                            </div>

                            <div class="wpo-service-single-item list-widget">
                                <div class="wpo-service-single-title">
                                    <h3>Key Features</h3>
                                </div>
                                <ul class="service-single-list">
                                    <li><strong>Vaccination Record Management</strong> Stores complete and up-to-date
                                        vaccination histories to ensure pets receive timely immunizations.</li>
                                    <li><strong>Pet Health Monitoring</strong> Tracks clinic visits, health status, and
                                        relevant medical notes over time.</li>
                                    <li><strong>Quick Scan Access</strong> Enables fast retrieval of pet records through a
                                        simple and secure scanning process.</li>
                                    <li><strong>Data Privacy and Security</strong> Protects pet and owner information by
                                        allowing access only to authorized personnel.</li>
                                </ul>
                            </div>

                            <div class="wpo-service-single-item list-widget">
                                <div class="wpo-service-single-title">
                                    <h3>Benefits</h3>
                                </div>
                                <ul class="service-single-list">
                                    <li>Faster and smoother clinic visits</li>
                                    <li>Reduced paperwork and manual record handling</li>
                                    <li>Improved accuracy in pet health documentation</li>
                                    <li>Better preventive care and early health monitoring</li>
                                    <li>Increased trust between clinics and pet owners</li>
                                </ul>
                            </div>

                            <div class="wpo-service-single-item">
                                <div class="wpo-service-single-title">
                                    <h3>Who Can Use This Service</h3>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-12">
                                        <div class="wpo-service-single-item list-widget service-single-compact">
                                            <ul class="service-single-list">
                                                <li>Veterinary clinics and staff</li>
                                                <li>Clinic administrators</li>
                                                <li>Pet owners seeking organized and reliable pet health records</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="wpo-service-single-item">
                                <div class="wpo-service-single-title">
                                    <h3>Why Choose PawCare</h3>
                                </div>
                                <p>As a student-developed system, PawCare demonstrates how technology can improve
                                    veterinary services by combining efficiency, privacy, and accessibility. This service
                                    supports modern veterinary care while promoting responsible pet ownership.</p>
                                <a href="contact.html" class="theme-btn-s2">Contact Us</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12">
                        <div class="blog-card">
                            <div class="image">
                                <img src="assets/images/blog/vaccine.jpg" alt="">
                            </div>
                            <div class="content">
                                <ul class="date">
                                    <li>By PawCare </li>
                                    <li>Jan 2026</li>
                                </ul>
                                <h2><a>Protection Starts with Prevention</a></h2>
                                <p>Timely vaccinations help protect pets from preventable diseases.</p>
                            </div>
                        </div>
                        <div class="blog-card">
                            <div class="image">
                                <img src="assets/images/blog/signs.jpg" alt="">
                            </div>
                            <div class="content">
                                <ul class="date">
                                    <li>By PawCare </li>
                                    <li>Jan 2026</li>
                                </ul>
                                <h2><a>Monitoring Health, One Visit at a Time</a></h2>
                                <p>Consistent tracking helps detect health issues early.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- service-single-area end -->


        <!-- start of wpo-site-footer-section -->
        <footer class="wpo-site-footer">
            <div class="wpo-upper-footer">
                <div class="container">
                    <div class="row">
                        <div class="col col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="widget about-widget">
                                <div class="widget-title">
                                    <h2>Trusted Pet Care Vet For
                                        Your Favorite Pets</h2>
                                </div>
                                <div class="social-widget">
                                    <ul>
                                        <li><a href="#"><i class="ti-facebook"></i></a></li>
                                        <li><a href="#"><i class="ti-twitter-alt"></i></a></li>
                                        <li><a href="#"><i class="ti-linkedin"></i></a></li>
                                        <li><a href="#"><i class="ti-instagram"></i></a></li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                        <div class="col col-lg-3 col-md-6 col-sm-12 col-12">
                            <div class="widget contact-widget">
                                <div class="widget-title">
                                    <h3>Contact</h3>
                                </div>
                                <ul>
                                    <li>pawcare@gmail.com</li>
                                    <li>(044) 919-8020</li>
                                    <li>MacArthur Hwy <br> Meycauayan City, Bulacan</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col col-lg-3 col-md-6 col-sm-12 col-12">
                            <div class="widget link-widget">
                                <div class="widget-title">
                                    <h3>Quick Link</h3>
                                </div>
                                <ul>
                                    <li><a href="about.html">About Us</a></li>
                                    <li><a href="service.html">Services</a></li>
                                    <li><a href="blog.html">Blog</a></li>
                                    <li><a href="contact.html">Contact Us</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div> <!-- end container -->
            </div>
            <div class="wpo-lower-footer">
                <div class="container-fluid">
                    <div class="row g-0">
                        <div class="col col-lg-6 col-12">
                        </div>
                        <div class="col col-lg-6 col-12">
                            <ul class="right">
                                <li><a href="privace.html"><span class="rolling-text">Privace & Policy</span> </a></li>
                                <li><a href="terms.html"><span class="rolling-text">Terms</span></a></li>
                                <li><a href="about.html"><span class="rolling-text">About us</span></a></li>
                                <li><a href="faq.html"><span class="rolling-text">FAQ</span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <!-- end of wpo-site-footer-section -->


    </div>
    <!-- end of page-wrapper -->

    <!-- All JavaScript files
    ================================================== -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <!-- Plugins for this template -->
    <script src="assets/js/modernizr.custom.js"></script>
    <script src="assets/js/jquery-plugin-collection.js"></script>
    <!-- Custom script for this template -->
    <script src="assets/js/script.js"></script>
</body>


</html>

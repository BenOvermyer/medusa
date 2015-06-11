<!DOCTYPE html>
<html>
<head>
    <title>@yield('pageTitle') | Royal Manticoran Navy Database</title>
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/foundation/5.4.0/css/normalize.min.css">
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/foundation/5.4.0/css/foundation.min.css">
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.4/css/jquery.ui.datepicker.min.css">
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.4/css/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="{{{ $serverUrl }}}/css/main.css">

</head>
<body class="@yield('bodyclasses')">
    <div class="container">
        <header class="row">
            @include( 'nav' )
            <h1>The Royal Manticoran Navy <small>The Official Honor Harrington Fan Association</small></h1>
        </header>

        <div class="row">
            @yield( 'content' )
        </div>

        <footer class="row">
            <p>Copyright &copy; 2008 &ndash; 2014 The Royal Manticoran Navy: The Official Honor Harrington Fan Association, Inc. Some Rights Reserved.
               Honor Harrington and all related materials are &copy; David Weber.</p>
        </footer>
    </div>

    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/foundation/5.4.0/js/foundation.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/foundation/5.4.0/js/foundation/foundation.topbar.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script>
        jQuery( document ).foundation();
    </script>
    <script src="{{{ $serverUrl }}}/js/bundle.min.js"></script>
    @yield( 'scriptFooter' )
</body>
</html>

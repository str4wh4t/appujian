<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta name="description" content="Aplikasi untuk ujian meliputi ujian masuk UM, UTBK, SBMPTN secara online di lingkungan Universitas Diponegoro Semarang">
{{--	<meta name="keywords" content="">--}}
	<meta name="author" content="UNDIP">

	<meta name="{{ csrf_name() }}" content="{{ csrf_token() }}">

	<title>@yield('title','UJIAN ONLINE UNDIP')</title>

	<link rel="apple-touch-icon" href="{{ asset('assets/icon/apple-touch-icon.png') }}">
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/icon/favicon.ico') }}">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CMuli:300,400,500,700" rel="stylesheet">

	@include('template.global_css')

</head>
<body class="vertical-layout vertical-compact-menu 2-columns   menu-expanded fixed-navbar" data-open="click" data-menu="vertical-compact-menu" data-col="2-columns">

	@include('template.top_nav')

	@include('template.side_menu')

	<div class="app-content content">
		<div class="content-wrapper">

			@yield('content')

		</div>
	</div>

	@include('template.footer')

	@include('template.global_js')

</body>
</html>

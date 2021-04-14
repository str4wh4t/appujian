<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta name="keywords" content="ujian online, online exam, latihan soal ujian">
	<meta name="author" content="UNDIP">
	
	<meta name="{{ csrf_name() }}" content="{{ csrf_token() }}">
	
	<title>@yield('title', APP_NAME)</title>
	<meta name="description" content="{{ APP_DESC }}">

	<link rel="apple-touch-icon" href="{{ asset('assets/icon/'. APP_FAVICON_APPLE .'.png') }}">
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/icon/'. APP_FAVICON .'.ico') }}">
	<link href="{{ asset('assets/yarn/node_modules/typeface-muli/index.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/yarn/node_modules/typeface-open-sans/index.css') }}" rel="stylesheet">

	@include('template.global_css')

</head>
<body class="vertical-layout vertical-compact-menu 2-columns menu-expanded fixed-navbar" data-open="click" data-menu="vertical-compact-menu" data-col="2-columns">

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

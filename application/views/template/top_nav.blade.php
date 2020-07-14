<!-- fixed-top-->
<nav class="header-navbar navbar-expand-md navbar navbar-with-menu fixed-top navbar-dark bg-primary navbar-shadow navbar-brand-center">
	<div class="navbar-wrapper">
		<div class="navbar-header">
			<ul class="nav navbar-nav flex-row">
				<li class="nav-item mobile-menu d-md-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu font-large-1"></i></a></li>
				<li class="nav-item"><a class="navbar-brand" href="{{ site_url('/') }}">
					{{-- <img class="brand-logo" alt="robust admin logo" src="../../../app-assets/images/logo/logo-light-sm.png"> --}}
					<h3 class="brand-text">UJIAN ONLINE UNDIP</h3></a></li>
					<li class="nav-item d-md-none"><a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile"><i class="fa fa-ellipsis-v"></i></a></li>
				</ul>
			</div>
			<div class="navbar-container content">
				<div class="collapse navbar-collapse" id="navbar-mobile">
					<ul class="nav navbar-nav mr-auto float-left">
						<li class="nav-item d-none d-md-block"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ft-menu">         </i></a></li>
					</ul>
					<ul class="nav navbar-nav float-right">

						<li class="dropdown dropdown-user nav-item">
							<a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
								<span class="avatar"><img src="{{ asset('assets/imgs/user_default.png') }}" alt="avatar"><i></i></span>  {{-- avatar-online --}}
								<span class="user-name">{{ get_nama_lengkap_user() }} [ {{ get_selected_role()->description }} ]</span>
							</a>
							<div class="dropdown-menu dropdown-menu-right">
								{{-- <a class="dropdown-item" href="user-profile.html"><i class="ft-user"></i> Edit Profile</a>
								<a class="dropdown-item" href="email-application.html"><i class="ft-mail"></i> My Inbox</a>
								<a class="dropdown-item" href="user-cards.html"><i class="ft-check-square"></i> Task</a>
								<a class="dropdown-item" href="chat-application.html"><i class="ft-message-square"></i> Chats</a>
								<div class="dropdown-divider"></div> --}}
{{--								<a class="dropdown-item" href="{{ site_url('admin/choose_role') }}"><i class="ft-message-square"></i> Pilih Role</a>--}}
								<a class="dropdown-item" href="{{ site_url('logout') }}"><i class="ft-power"></i> Logout</a>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</nav>

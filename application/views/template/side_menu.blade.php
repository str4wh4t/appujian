<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
	<div class="main-menu-content">
		<ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
			@php
			$page = segment(1);
			$method = segment(2);
			$param1 = segment(3);
			$master = ["jurusan", "kelas", "matkul", "dosen", "mahasiswa"];
			$relasi = ["kelasdosen", "jurusanmatkul"];
			$users = ["users"];
			@endphp

			{{-- <li class="navigation-header" style="background-color: #ffcb60;text-align: left;">
				<span data-i18n="nav.category.ui">MAIN MENU</span>
				<i class="ft-more-horizontal ft-minus" data-toggle="tooltip" data-placement="right" data-original-title="MAIN MENU"></i>
          	</li> --}}
			<li class="{{ $page == 'dashboard' ? "active" : "" }} nav-item">
				<a href="{{ url('dashboard') }}">
					<i class="icon-home"></i><span class="menu-title" data-i18n="nav.dash.main">Dashboard</span>
				</a>
			</li>

			@if(is_admin())
			<li class="{{ in_array($page, $master)  ? "active menu-open" : "" }} nav-item">
                <a href="{{ url('matkul') }}">
                    <i class="fa fa-folder"></i><span class="menu-title" data-i18n="nav.templates.main">Master</span>
                </a>
                <ul class="menu-content">
					<li class="{{ $page=='matkul'?"active":"" }}">
						<a class="menu-item" href="{{ url('matkul') }}">
							<i class="fa {{ $page == 'matkul' ? "fa-play-circle-o" : "fa-circle-o" }}"></i>
							Materi Ujian
						</a>
					</li>
					<li class="{{ $page=='topik'?"active":"" }}">
						<a class="menu-item" href="{{ url('topik') }}">
							<i class="fa {{ $page == 'topik' ? "fa-play-circle-o" : "fa-circle-o" }}"></i>
							Topik
						</a>
					</li>
					<li class="{{ $page=='mahasiswa'?"active":"" }}">
						<a class="menu-item" href="{{ url('mahasiswa') }}">
							<i class="fa {{ $page == 'mahasiswa' ? "fa-play-circle-o" : "fa-circle-o" }}"></i>
							Peserta Ujian
						</a>
					</li>
					@if($_ENV['IS_ENABLE_DOSEN'])
					<li class="{{ $page=='dosen'?"active":"" }}">
						<a class="menu-item" href="{{ url('dosen') }}">
							<i class="fa {{ $page == 'dosen' ? "fa-play-circle-o" : "fa-circle-o" }}"></i>
							Dosen
						</a>
					</li>
					@endif

{{--					<li>--}}
{{--						<a class="menu-item" href="#" data-i18n="nav.components.components_buttons.main">Group</a>--}}
{{--						<ul class="menu-content">--}}
{{--							<li class="{{ $page=='jurusan'?"active":"" }}">--}}
{{--								<a class="menu-item" href="{{ url('jurusan') }}" data-i18n="nav.components.components_buttons.component_buttons_basic">--}}
{{--									<i class="fa fa-circle-o"></i> Jurusan--}}
{{--								</a>--}}
{{--							</li>--}}
{{--							<li class="{{ $page=='kelas'?"active":"" }}">--}}
{{--								<a class="menu-item" href="{{ url('kelas') }}" data-i18n="nav.components.components_buttons.component_buttons_extended">--}}
{{--									<i class="fa fa-circle-o"></i> Kelas--}}
{{--								</a>--}}
{{--							</li>--}}
{{--						</ul>--}}
{{--					</li>--}}

{{--						<li class="{{ $page=='jurusan'?"active":"" }}">--}}
{{--							<a class="menu-item" href="{{ url('jurusan') }}">--}}
{{--								<i class="fa fa-circle-o"></i> Jurusan--}}
{{--							</a>--}}
{{--						</li>--}}
{{--						<li class="{{ $page=='kelas'?"active":"" }}">--}}
{{--							<a class="menu-item" href="{{ url('kelas') }}">--}}
{{--								<i class="fa fa-circle-o"></i> Kelas--}}
{{--							</a>--}}
{{--						</li>--}}

				</ul>
			</li>

			<li class="{{ $page == 'soal' ? "active" : "" }} nav-item">
                <a href="{{ url('soal') }}">
                    <i class="fa fa-folder"></i><span class="menu-title" data-i18n="nav.templates.main">Soal Manaj.</span>
                </a>
                <ul class="menu-content">
					<li class="{{ $method  == 'bobot_soal' ? "active":"" }}">
						<a class="menu-item" href="{{ url('soal/bobot_soal') }}">
							<i class="fa {{ $method  == 'bobot_soal' ? "fa-play-circle-o" : "fa-circle-o" }}"></i>
							Bobot Soal
						</a>
					</li>
					<li class="{{ ($page == 'soal') && $method == 'index' ? "active":"" }}">
						<a class="menu-item" href="{{ url('soal/index') }}">
							<i class="fa {{ ($page == 'soal') && $method  == 'index' ? "fa-play-circle-o" : "fa-circle-o" }}"></i>
							List Soal
						</a>
					</li>
					<li class="{{ $method == 'bundle_soal' ? "active":"" }}">
						<a class="menu-item" href="{{ url('soal/bundle_soal') }}">
							<i class="fa {{ $method  == 'bundle_soal' ? "fa-play-circle-o" : "fa-circle-o" }}"></i>
							Bundle Soal
						</a>
					</li>
				</ul>
			</li>
			@endif

			@if(in_group(DOSEN_GROUP_ID) || in_group(PENYUSUN_SOAL_GROUP_ID))
			<li class="{{ $page == 'soal' ? "active" : "" }} nav-item">
				<a href="{{ url('soal') }}">
					<i class="fa fa-file-text-o"></i><span class="menu-title" data-i18n="nav.dash.main">Soal Manaj.</span>
				</a>
			</li>
			@endif

			@if( is_admin() || in_group(DOSEN_GROUP_ID) || in_group(PENGAWAS_GROUP_ID) || in_group(KOORD_PENGAWAS_GROUP_ID) )
			
			@if( $_ENV['APP_TYPE'] == 'tryout' )
			<li class="{{ ($page == 'ujian') || ($page == 'paket') ? "active" : "" }} nav-item">
			<a href="{{ url('ujian/master') }}">
				<i class="fa fa-folder"></i><span class="menu-title" data-i18n="nav.templates.main">Ujian Manaj.</span>
			</a>
				<ul class="menu-content">
					<li class="{{ $method == 'master' ? "active":"" }}">
						<a class="menu-item" href="{{ url('ujian/master') }}">
							<i class="fa {{ $method  == 'master' ? "fa-play-circle-o" : "fa-circle-o" }}"></i>
							List Ujian
						</a>
					</li>
					@if( is_admin() )
					<li class="{{ ($page == 'paket') && ($method == 'index') ? "active":"" }}">
						<a class="menu-item" href="{{ url('paket/index') }}">
							<i class="fa {{ ($page == 'paket') && $method  == 'index' ? "fa-play-circle-o" : "fa-circle-o" }}"></i>
							Paket Ujian
						</a>
					</li>
					@endif
				</ul>
			</li>

			@else 

			<li class="{{ $page == 'ujian' ? "active" : "" }} nav-item">
				<a href="{{ url('ujian/master') }}">
					<i class="fa fa-calendar"></i><span class="menu-title" data-i18n="nav.dash.main">Ujian Manaj.</span>
				</a>
			</li>
			
			@endif
			@endif

			@if( in_group(MHS_GROUP_ID) )
				@if( $_ENV['APP_TYPE'] == 'tryout' )
					@if(is_show_membership())
					<li class="{{ $page == 'membership' ? "active" : "" }} nav-item">
						<a href="{{ url('membership/list') }}">
							<i class="icon-diamond"></i><span class="menu-title" data-i18n="nav.dash.main">Membership</span>
						</a>
					</li>
					@endif

					@if(is_show_paket())
					<li class="{{ $page == 'paket' ? "active" : "" }} nav-item">
						<a href="{{ url('paket/list') }}">
							<i class="icon-basket-loaded"></i><span class="menu-title" data-i18n="nav.dash.main">Paket Materi</span>
						</a>
					</li>
					@endif
				<li class="{{ ($page == 'ujian' && $method == 'latihan_soal' ) ? "active" : "" }} nav-item">
					<a href="{{ url('ujian/latihan_soal') }}">
						{{-- <i class="ft-edit-3"></i><span class="menu-title" data-i18n="nav.dash.main">Latihan Soal</span> --}}
						<i class="ft-edit-3"></i><span class="menu-title" data-i18n="nav.dash.main">Tryout</span>
					</a>
				</li>

				{{-- TRYOUT SEMENTARA DI HIDDEN DULU --}}
				{{-- <li class="{{ ($page == 'ujian' && $method == 'tryout' ) ? "active" : "" }} nav-item">
					<a href="{{ url('ujian/tryout') }}">
						<i class="fa fa-trophy"></i><span class="menu-title" data-i18n="nav.dash.main">Tryout</span>
					</a>
				</li> --}}

				@else
				<li class="{{ $page == 'ujian' ? "active" : "" }} nav-item">
					<a href="{{ url('ujian/list') }}">
						<i class="fa fa-calendar"></i><span class="menu-title" data-i18n="nav.dash.main">Ujian</span>
					</a>
				</li>
				@endif
			@endif

			@if( is_admin() || in_group(DOSEN_GROUP_ID) )
			{{-- <li class="navigation-header" style="background-color: #ffcb60;text-align: left;">
				<span data-i18n="nav.category.ui">LAPORAN</span>
				<i class="ft-more-horizontal ft-minus" data-toggle="tooltip" data-placement="right" data-original-title="LAPORAN"></i>
          	</li> --}}
			<li class="{{ $page == 'hasilujian' ? "active" : "" }} nav-item">
				<a href="{{ url('hasilujian') }}">
					<i class="fa fa-file"></i><span class="menu-title" data-i18n="nav.dash.main">Hasil Ujian</span>
				</a>
			</li>
			@endif

			@if(is_admin() || in_group(KOORD_PENGAWAS_GROUP_ID))
			{{-- <li class="navigation-header" style="background-color: #ffcb60;text-align: left;">
				<span data-i18n="nav.category.ui">SETTING</span>
				<i class="ft-more-horizontal ft-minus" data-toggle="tooltip" data-placement="right" data-original-title="ADMINISTRATOR"></i>
          	</li> --}}
			
			@if(is_admin())
				@if( $_ENV['APP_TYPE'] == 'tryout' )
				<li class="{{ $page == 'payment' ? "active" : "" }} nav-item">
					<a href="{{ url('payment/order_list') }}">
						<i class="fa fa-shopping-bag"></i><span class="menu-title" data-i18n="nav.dash.main">Payment</span>
					</a>
					<ul class="menu-content">
						<li class="{{ $method  == 'order_list' ? "active":"" }}">
							<a class="menu-item" href="{{ url('payment/order_list') }}">
								<i class="fa {{ $method  == 'order_list' ? "fa-play-circle-o" : "fa-circle-o" }}"></i>
								Order List
							</a>
						</li>
					</ul>
				</li>
				@endif
			@endif

			<li class="{{ (($page == 'users') && ($method != 'edit' || !empty($param1)))  ? "active" : "" }} nav-item">
				<a href="{{ url('users') }}">
					<i class="fa fa-users"></i><span class="menu-title" data-i18n="nav.dash.main">User Manaj.</span>
				</a>
			</li>

			@endif

			@if( !is_admin() )
			{{-- <li class="navigation-header" style="background-color: #ffcb60;text-align: left;">
				<span data-i18n="nav.category.ui">SETTING</span>
				<i class="ft-more-horizontal ft-minus" data-toggle="tooltip" data-placement="right" data-original-title="ADMINISTRATOR"></i>
          	</li> --}}
			<li class="{{ ($page == 'users' && $method == 'edit' && empty($param1))? "active" : "" }} nav-item">
				<a href="{{ url('users/edit') }}">
					<i class="fa fa-cog"></i><span class="menu-title" data-i18n="nav.dash.main">Setting</span>
				</a>
			</li>
			@endif

		</ul>
	</div>
</div>



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
			<li class="{{ $page === 'dashboard' ? "active" : "" }} nav-item">
				<a href="{{ site_url('dashboard') }}">
					<i class="icon-home"></i><span class="menu-title" data-i18n="nav.dash.main">Dashboard</span>
				</a>
			</li>

			@if(is_admin())
			<li class="{{ in_array($page, $master)  ? "active menu-open" : "" }} nav-item">
                <a href="{{ site_url('matkul') }}">
                    <i class="fa fa-folder"></i><span class="menu-title" data-i18n="nav.templates.main">Master</span>
                </a>
                <ul class="menu-content">
					<li class="{{ $page==='matkul'?"active":"" }}">
						<a class="menu-item" href="{{ site_url('matkul') }}">
							<i class="fa {{ $page === 'matkul' ? "fa-play-circle-o" : "fa-circle-o" }}"></i>
							Materi Ujian
						</a>
					</li>
					<li class="{{ $page==='topik'?"active":"" }}">
						<a class="menu-item" href="{{ site_url('topik') }}">
							<i class="fa {{ $page === 'topik' ? "fa-play-circle-o" : "fa-circle-o" }}"></i>
							Topik
						</a>
					</li>
					<li class="{{ $page==='mahasiswa'?"active":"" }}">
						<a class="menu-item" href="{{ site_url('mahasiswa') }}">
							<i class="fa {{ $page === 'mahasiswa' ? "fa-play-circle-o" : "fa-circle-o" }}"></i>
							Peserta Ujian
						</a>
					</li>
					<li class="{{ $page==='dosen'?"active":"" }}">
						<a class="menu-item" href="{{ site_url('dosen') }}">
							<i class="fa {{ $page === 'dosen' ? "fa-play-circle-o" : "fa-circle-o" }}"></i>
							Dosen
						</a>
					</li>

{{--					<li>--}}
{{--						<a class="menu-item" href="#" data-i18n="nav.components.components_buttons.main">Group</a>--}}
{{--						<ul class="menu-content">--}}
{{--							<li class="{{ $page==='jurusan'?"active":"" }}">--}}
{{--								<a class="menu-item" href="{{ site_url('jurusan') }}" data-i18n="nav.components.components_buttons.component_buttons_basic">--}}
{{--									<i class="fa fa-circle-o"></i> Jurusan--}}
{{--								</a>--}}
{{--							</li>--}}
{{--							<li class="{{ $page==='kelas'?"active":"" }}">--}}
{{--								<a class="menu-item" href="{{ site_url('kelas') }}" data-i18n="nav.components.components_buttons.component_buttons_extended">--}}
{{--									<i class="fa fa-circle-o"></i> Kelas--}}
{{--								</a>--}}
{{--							</li>--}}
{{--						</ul>--}}
{{--					</li>--}}

{{--						<li class="{{ $page==='jurusan'?"active":"" }}">--}}
{{--							<a class="menu-item" href="{{ site_url('jurusan') }}">--}}
{{--								<i class="fa fa-circle-o"></i> Jurusan--}}
{{--							</a>--}}
{{--						</li>--}}
{{--						<li class="{{ $page==='kelas'?"active":"" }}">--}}
{{--							<a class="menu-item" href="{{ site_url('kelas') }}">--}}
{{--								<i class="fa fa-circle-o"></i> Kelas--}}
{{--							</a>--}}
{{--						</li>--}}

				</ul>
			</li>

{{--			<li class="{{ in_array($page, $relasi)  ? "active menu-open" : "" }} nav-item">--}}
{{--                <a href="{{ site_url('login') }}">--}}
{{--                    <i class="fa fa-link"></i><span class="menu-title" data-i18n="nav.templates.main">Relasi</span>--}}
{{--                </a>--}}
{{--				<ul class="menu-content">--}}
{{--					<li class="{{ $page==='kelasdosen'?"active":"" }}">--}}
{{--						<a class="menu-item" href="{{ site_url('kelasdosen') }}">--}}
{{--							<i class="fa fa-circle-o"></i>--}}
{{--							Kelas - Dosen--}}
{{--						</a>--}}
{{--					</li>--}}
{{--					<li class="{{ $page==='jurusanmatkul'?"active":"" }}">--}}
{{--						<a class="menu-item" href="{{ site_url('jurusanmatkul') }}">--}}
{{--							<i class="fa fa-circle-o"></i>--}}
{{--							Jurusan - Mata Kuliah--}}
{{--						</a>--}}
{{--					</li>--}}
{{--				</ul>--}}
{{--			</li>--}}

			<li class="{{ $page === 'soal' ? "active" : "" }} nav-item">
                <a href="{{ site_url('soal') }}">
                    <i class="fa fa-folder"></i><span class="menu-title" data-i18n="nav.templates.main">Soal Manaj.</span>
                </a>
				@php($method = segment(2))
                <ul class="menu-content">
					<li class="{{ $method  === 'bobot_soal' ? "active":"" }}">
						<a class="menu-item" href="{{ site_url('soal/bobot_soal') }}">
							<i class="fa {{ $method  === 'bobot_soal' ? "fa-play-circle-o" : "fa-circle-o" }}"></i>
							Bobot Soal
						</a>
					</li>
					<li class="{{ $method === 'index' ? "active":"" }}">
						<a class="menu-item" href="{{ site_url('soal/index') }}">
							<i class="fa {{ $method  === 'index' ? "fa-play-circle-o" : "fa-circle-o" }}"></i>
							List Soal
						</a>
					</li>
				</ul>
			</li>
			@endif

			@if(in_group('dosen') || in_group('penyusun_soal'))
			<li class="{{ $page === 'soal' ? "active" : "" }} nav-item">
				<a href="{{ site_url('soal') }}">
					<i class="fa fa-file-text-o"></i><span class="menu-title" data-i18n="nav.dash.main">Soal Manaj.</span>
				</a>
			</li>
			@endif

			@if( is_admin() || in_group('dosen') || in_group('pengawas') )
			<li class="{{ $page === 'ujian' ? "active" : "" }} nav-item">
				<a href="{{ site_url('ujian/master') }}">
					<i class="fa fa-calendar"></i><span class="menu-title" data-i18n="nav.dash.main">Ujian Manaj.</span>
				</a>
			</li>
			@endif

			@if( in_group('mahasiswa') )
				@if( APP_ID == 'tryout.undip.id' )
				<li class="{{ $page === 'membership' ? "active" : "" }} nav-item">
					<a href="{{ site_url('membership/list') }}">
						<i class="icon-diamond"></i><span class="menu-title" data-i18n="nav.dash.main">Membership</span>
					</a>
				</li>
				<li class="{{ $page === 'paket' ? "active" : "" }} nav-item">
					<a href="{{ site_url('paket/list') }}">
						<i class="icon-basket-loaded"></i><span class="menu-title" data-i18n="nav.dash.main">Paket Materi</span>
					</a>
				</li>
				<li class="{{ ($page === 'ujian' && $method === 'latian_soal' ) ? "active" : "" }} nav-item">
					<a href="{{ site_url('ujian/latian_soal') }}">
						<i class="ft-edit-3"></i><span class="menu-title" data-i18n="nav.dash.main">Latian Soal</span>
					</a>
				</li>
				<li class="{{ ($page === 'ujian' && $method === 'tryout' ) ? "active" : "" }} nav-item">
					<a href="{{ site_url('ujian/tryout') }}">
						<i class="fa fa-trophy"></i><span class="menu-title" data-i18n="nav.dash.main">Tryout</span>
					</a>
				</li>
				@else
				<li class="{{ $page === 'ujian' ? "active" : "" }} nav-item">
					<a href="{{ site_url('ujian/list') }}">
						<i class="fa fa-calendar"></i><span class="menu-title" data-i18n="nav.dash.main">Ujian</span>
					</a>
				</li>
				@endif
			@endif

			@if( is_admin() || in_group('dosen') )
			{{-- <li class="navigation-header" style="background-color: #ffcb60;text-align: left;">
				<span data-i18n="nav.category.ui">LAPORAN</span>
				<i class="ft-more-horizontal ft-minus" data-toggle="tooltip" data-placement="right" data-original-title="LAPORAN"></i>
          	</li> --}}
			<li class="{{ $page === 'hasilujian' ? "active" : "" }} nav-item">
				<a href="{{ site_url('hasilujian') }}">
					<i class="fa fa-file"></i><span class="menu-title" data-i18n="nav.dash.main">Hasil Ujian</span>
				</a>
			</li>
			@endif

			@if(is_admin())
			{{-- <li class="navigation-header" style="background-color: #ffcb60;text-align: left;">
				<span data-i18n="nav.category.ui">SETTING</span>
				<i class="ft-more-horizontal ft-minus" data-toggle="tooltip" data-placement="right" data-original-title="ADMINISTRATOR"></i>
          	</li> --}}
			<li class="{{ $page === 'users' ? "active" : "" }} nav-item">
				<a href="{{ site_url('users') }}">
					<i class="fa fa-users"></i><span class="menu-title" data-i18n="nav.dash.main">User Manaj.</span>
				</a>
			</li>
{{--			<li class="{{ $page === 'settings' ? "active" : "" }} nav-item">--}}
{{--				<a href="{{ site_url('settings') }}">--}}
{{--					<i class="fa fa-cog"></i><span class="menu-title" data-i18n="nav.dash.main">Setting</span>--}}
{{--				</a>--}}
{{--			</li>--}}
			@endif

			@if( in_group('dosen') || in_group('mahasiswa') || in_group('penyusun_soal') )
			{{-- <li class="navigation-header" style="background-color: #ffcb60;text-align: left;">
				<span data-i18n="nav.category.ui">SETTING</span>
				<i class="ft-more-horizontal ft-minus" data-toggle="tooltip" data-placement="right" data-original-title="ADMINISTRATOR"></i>
          	</li> --}}
			<li class="{{ $page === 'users' ? "active" : "" }} nav-item">
				<a href="{{ site_url('users/edit') }}">
					<i class="fa fa-cog"></i><span class="menu-title" data-i18n="nav.dash.main">Setting</span>
				</a>
			</li>
			@endif

		</ul>
	</div>
</div>



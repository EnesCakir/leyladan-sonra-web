<section class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel">
        <div class="pull-left image">
            <img src="{{ $authUser->photo_small_url }}" class="img-circle" alt="{{ $authUser->full_name }}">
        </div>
        <div class="pull-left info">
            <p>{{ $authUser->full_name }}</p>
            <!-- Status -->
            <a href="#"><i class="fa fa-black-tie"></i> {{ $authUser->role_display }}</a>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <ul class="sidebar-menu" data-widget="tree">
        <li class="{{ set_active('*admin/dashboard*') }}">
            <a href="{{ route('admin.dashboard') }}"><i class="fa fa-compass"></i> <span>Kontrol Paneli</span></a>
        </li>
        <li class="{{ set_active('*admin/profile') }}">
            <a href="{{ route('admin.profile.index') }}"><i class="fa fa-address-book-o"></i> <span>Arkadaşlarım</span></a>
        </li>
        <li class="treeview {{ set_active('*child*', 'menu-open active') }}">
            <a href="#"><i class="fa fa-child"></i> <span>Çocuklar</span>
                <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
            </a>
            <ul class="treeview-menu">
                <li class="{{ set_active('*admin/child/create*') }}">
                    <a href="{{ route('admin.child.create') }}"><i class="fa fa-plus"></i> <span>Yeni Çocuk Ekle</span></a>
                </li>
                <li class="{{ set_active('*admin/profile/child') }}">
                    <a href="{{ route('admin.profile.show') }}"><i class="fa fa-heart"></i>
                        <span>Kendi Çocuklarım</span></a>
                </li>
                <li class="{{ set_active('*admin/faculty/*/child') }}">
                    <a href="{{ route('admin.faculty.child.index', $authUser->faculty_id) }}"><i class="fa fa-bars"></i>
                        <span>Fakülte Çocukları</span></a>
                </li>
                <li class="{{ set_active('*admin/child') }}">
                    <a href="{{ route('admin.child.index') }}"><i class="fa fa-list"></i>
                        <span>Bütün Çocuklar</span></a>
                </li>
            </ul>
        </li>
        <li class="treeview {{ set_active(['*admin/faculty', '*form*', '*admin/faculty/create', '*admin/faculty/*/edit'], 'menu-open active') }}">
            <a href="#"><i class="fa fa-university"></i> <span>Fakülteler</span>
                <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
            </a>
            <ul class="treeview-menu">
                <li class="{{ set_active('*admin/faculty/create') }}">
                    <a href="{{ route('admin.faculty.create') }}"><i class="fa fa-plus"></i>
                        <span>Yeni Fakülte Ekle</span></a>
                </li>
                <li class="{{ set_active('*admin/faculty') }}">
                    <a href="{{ route('admin.faculty.index') }}"><i class="fa fa-fort-awesome"></i>
                        <span>Bütün Fakülteler</span></a>
                </li>
                <li class="{{ set_active('*admin/form/create*') }}">
                    <a href="{{ route('admin.form.create') }}"><i class="fa fa-file-text-o"></i>
                        <span>Onam Formu Oluştur</span></a>
                </li>
            </ul>
        </li>
        <li class="treeview {{ set_active('*blood*', 'menu-open active') }}">
            <a href="#"><i class="fa fa-tint"></i> <span>Kan Bağışı</span>
                <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
            </a>
            <ul class="treeview-menu">
                <li class="{{ set_active('*admin/blood/create*') }}">
                    <a href="{{ route('admin.blood.create') }}"><i class="fa fa-plus"></i>
                        <span>Yeni Bağışçı Ekle</span></a>
                </li>
                <li class="{{ set_active('*admin/blood') }}">
                    <a href="{{ route('admin.blood.index') }}"><i class="fa fa-users"></i>
                        <span>Tüm Bağışçılar</span></a>
                </li>
                <li class="{{ set_active('*admin/blood/sms/send*') }}">
                    <a href="{{ route('admin.blood.sms.send') }}"><i class="fa fa-paper-plane"></i>
                        <span>SMS Gönder</span></a>
                </li>
                <li class="{{ set_active('*admin/blood/sms*') }}">
                    <a href="{{ route('admin.blood.sms.index') }}"><i class="fa fa-envelope"></i> <span>Gönderilen Mesajlar</span></a>
                </li>
                <li class="{{ set_active('*admin/blood/people*') }}">
                    <a href="{{ route('admin.blood.people.edit') }}"><i class="fa fa-user-circle-o"></i> <span>Görevliler</span></a>
                </li>
            </ul>
        </li>
        <li class="treeview {{ set_active('*post*', 'menu-open active') }}">
            <a href="#"><i class="fa fa-pencil"></i> <span>Yazılar</span>
                <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
            </a>
            <ul class="treeview-menu">
                <li>
                    <a href="{{ route('admin.faculty.post.index', [$authUser->faculty_id, 'approval' => '0']) }}"><i
                                class="fa fa-thumbs-o-up"></i> <span>Onay Bekleyenler</span>
                        <span class="pull-right-container">
                            <small class="label pull-right bg-red unapproved-post-count" data-toggle="tooltip" title="Onaylanmamış Sohbet Sayısı"></small>
                        </span>

                    </a>
                </li>
                <li class="{{ set_active('*admin/faculty/*/post') }}">
                    <a href="{{ route('admin.faculty.post.index', $authUser->faculty_id) }}"><i
                                class="fa fa-folder-o"></i>
                        <span>Fakülte Yazılar</span></a>
                </li>
                <li class="{{ set_active('*admin/post') }}">
                    <a href="{{ route('admin.post.index') }}"><i class="fa fa-folder-open-o"></i>
                        <span>Tüm Yazılar</span></a>
                </li>
            </ul>
        </li>
        <li class="treeview {{ set_active(['*chat*', '*volunteer*'], 'menu-open active') }}">
            <a href="#"><i class="fa fa-trophy"></i> <span>Gönüllüler</span>
                <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
            </a>
            <ul class="treeview-menu">
                <li class="">
                    <a href="{{ route('admin.faculty.chat.index', [$authUser->faculty_id, 'status' => 'active']) }}"><i
                                class="fa fa-commenting-o"></i>
                        <span>Aktif Sohbetler</span>
                        <span class="pull-right-container">
                            <small class="label pull-right bg-red open-chat-count" data-toggle="tooltip" title="Açık Sohbet Sayısı"></small>
                        </span>
                    </a>
                </li>
                <li class="{{ set_active('*admin/faculty/*/chat') }}">
                    <a href="{{ route('admin.faculty.chat.index', $authUser->faculty_id) }}"><i
                                class="fa fa-comment-o"></i>
                        <span>Fakülte Sohbetleri</span></a>
                </li>
                <li class="{{ set_active('*admin/chat') }}">
                    <a href="{{ route('admin.chat.index') }}"><i
                                class="fa fa-comments"></i>
                        <span>Tüm Sohbetler</span></a>
                </li>

                <li class="{{ set_active('*admin/volunteer') }}">
                    <a href="{{ route('admin.volunteer.index') }}"><i
                                class="fa fa-star"></i>
                        <span>Tüm Gönüllüler</span></a>
                </li>
                {{--<li><a href="#"><i class="fa fa-bell-o"></i> <span>Bildirim Gönder</span></a></li>--}}
            </ul>
        </li>
        <li class="treeview  {{ set_active('*user*', 'menu-open active') }}">
            <a href="#"><i class="fa fa-users"></i> <span>Üyeler</span>
                <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
            </a>
            <ul class="treeview-menu">
                <li class="">
                    <a href="{{ route('admin.faculty.user.index', ['faculty_id' => $authUser->faculty_id, 'approval' => 0]) }}">
                        <i class="fa fa-thumbs-o-up"></i>
                        <span>Onay Bekleyenler</span>
                        <span class="pull-right-container">
                            <small class="label pull-right bg-red unapproved-user-count" data-toggle="tooltip" title="Onaylanmamış Üye Sayısı"></small>
                        </span>
                    </a>
                </li>
                <li class="{{ set_active('*admin/faculty/*/user*') }}">
                    <a href="{{ route('admin.faculty.user.index', $authUser->faculty_id) }}"><i class="fa fa-user"></i>
                        <span>Fakülte Üyeleri</span></a>
                </li>
                <li class="{{ set_active('*admin/user*') }}">
                    <a href="{{ route('admin.user.index') }}"><i class="fa fa-users"></i> <span>Tüm Üyeler</span></a>
                </li>
                <li class="{{ set_active('*admin/faculty/*/email*') }}">
                    <a href="{{ route('admin.faculty.email.create', $authUser->faculty_id) }}"><i
                                class="fa fa-paper-plane"></i>
                        <span>E-posta Gönder</span></a>
                </li>
            </ul>
        </li>
        <li class="treeview {{ set_active(['*diagnosis*', '*department*'], 'menu-open active') }}">
            <a href="#"><i class="fa fa-cog"></i> <span>Ayarlar</span>
                <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
            </a>
            <ul class="treeview-menu">
                <li class="{{ set_active('*admin/diagnosis*') }}">
                    <a href="{{ route('admin.diagnosis.index') }}"><i class="fa fa-flask"></i> <span>Tanılar</span></a>
                </li>
                <li class="{{ set_active('*admin/department*') }}">
                    <a href="{{ route('admin.department.index') }}"><i class="fa fa-hospital-o"></i>
                        <span>Departmanlar</span></a>
                </li>
            </ul>
        </li>

        <li class="header">Site</li>
        <li class="treeview {{ set_active(['*new*', '*channel*'], 'menu-open active') }}">
            <a href="#"><i class="fa fa-newspaper-o"></i> <span>Basında Biz</span>
                <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
            </a>
            <ul class="treeview-menu">
                <li class="{{ set_active('*admin/new/create*') }}">
                    <a href="{{ route('admin.new.create') }}"><i class="fa fa-plus"></i>
                        <span>Yeni Haber Ekle</span></a>
                </li>
                <li class="{{ set_active('*admin/new') }}">
                    <a href="{{ route('admin.new.index') }}"><i class="fa fa-list-alt"></i>
                        <span>Tüm Haberler</span></a>
                </li>
                <li class="{{ set_active('*admin/channel/create*') }}">
                    <a href="{{ route('admin.channel.create') }}"><i class="fa fa-plus"></i>
                        <span>Yeni Kanal Ekle</span></a>
                </li>
                <li class="{{ set_active('*admin/channel') }}">
                    <a href="{{ route('admin.channel.index') }}"><i class="fa fa-television"></i>
                        <span>Tüm Kanallar</span></a>
                </li>
            </ul>
        </li>
        <li class="{{ set_active('*admin/testimonial*') }}">
            <a href="{{ route('admin.testimonial.index') }}"><i class="fa fa-comment-o"></i>
                <span>Referanslar</span></a>
        </li>
        <li class="{{ set_active('*admin/sponsor*') }}">
            <a href="{{ route('admin.sponsor.index') }}"><i class="fa fa-briefcase"></i>
                <span>Destekçiler</span></a>
        </li>
        <li class="{{ set_active('*admin/question*') }}">
            <a href="{{ route('admin.question.index') }}"><i class="fa fa-question-circle-o"></i>
                <span>Sorular</span></a>
        </li>
        <li class="header">Diğer</li>
        <li class="treeview">
            <a href="#"><i class="fa fa-bar-chart"></i> <span>İstatistikler</span>
                <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
            </a>
            <ul class="treeview-menu">
                <li><a href="#"><i class="fa fa-child"></i> <span>çocuklar</span></a></li>
                <li><a href="#"><i class="fa fa-university"></i> <span>fakülteler</span></a></li>
                <li><a href="#"><i class="fa fa-trophy"></i> <span>gönüllüler</span></a></li>
                <li><a href="#"><i class="fa fa-tint"></i> <span>kan bağışçıları</span></a></li>
                <li><a href="#"><i class="fa fa-users"></i> <span>üyeler</span></a></li>
                <li><a href="#"><i class="fa fa-globe"></i> <span>site ziyareti</span></a></li>
            </ul>
        </li>
        <li class="{{ set_active('*admin/material*') }}">
            <a href="{{ route('admin.material.index') }}"><i class="fa fa-bullhorn"></i>
                <span>Tanıtım Materyalleri</span></a>
        </li>
        <li class="{{ set_active('*admin/emailsample*') }}">
            <a href="{{ route('admin.emailsample.index') }}"><i class="fa fa-envelope-open-o"></i> <span>E-posta Örnekleri</span></a>
        </li>
        <li class="{{ set_active('*admin/tutorial*') }}">
            <a href="{{ route('admin.tutorial.index') }}"><i class="fa fa-graduation-cap"></i>
                <span>Kullanma Kılavuzu</span></a>
        </li>
    </ul>
    <!-- /.sidebar-menu -->
</section>

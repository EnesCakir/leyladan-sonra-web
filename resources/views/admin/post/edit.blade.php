@extends('admin.layouts.app')

@section('title', $post->child->full_name)

@section('header')
    <section class="content-header">
        <h1>
            {{ $post->child->full_name }}
            <small><strong>{{ $post->type}}</strong> yazısını düzenliyorsunuz</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i> Anasayfa</a></li>
            <li><a href="{{ route('admin.post.index') }}">Tüm Yazılar</a></li>
            <li>Yazı Düzenleme</li>
            <li class="active">{{ $post->child->full_name }}</li>
        </ol>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <!-- Horizontal Form -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4 class="box-title">Çocuğun Ayrıntılı Bilgileri</h4>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <div class="box-body no-padding">
                    <table class="table table-striped table-bordered">
                        <tbody>
                        <tr>
                            <th>Ad</th>
                            <td>{{ $post->child->first_name }}</td>
                        </tr>
                        <tr>
                            <th>Soyad</th>
                            <td>{{ $post->child->last_name }}</td>
                        </tr>
                        <tr>
                            <th>Fakülte</th>
                            <td>{{ $post->child->faculty->name }}</td>
                        </tr>
                        <tr>
                            <th>Tanı</th>
                            <td>{{ $post->child->diagnosis }}</td>
                        </tr>
                        <tr>
                            <th>Tanı Açıklama</th>
                            <td>{{ $post->child->diagnosis_desc }}</td>
                        </tr>
                        <tr>
                            <th>Alınan Tedaviler</th>
                            <td>{{ $post->child->taken_treatment }}</td>
                        </tr>
                        <tr>
                            <th>Doğum Günü</th>
                            <td>{{ $post->child->birthday_label }}</td>
                        </tr>
                        <tr>
                            <th>Bizden İsteği</th>
                            <td>{{ $post->child->wish }}</td>
                        </tr>
                        <tr>
                            <th>Departman</th>
                            <td>{{ $post->child->department }}</td>
                        </tr>
                        <tr>
                            <th>Sorumlular</th>
                            <td>{{ $post->child->users->implode('full_name', ', ') }}</td>
                        </tr>
                        <tr>
                            <th>Ekstra Bilgi</th>
                            <td>{{ $post->child->extra_info }}</td>
                        </tr>
                        <tr>
                            <th>Site Bağlantısı</th>
                            <td>
                                <a target="_blank"
                                   href="{{ route("front.child", [$post->child->faculty->slug, $post->child->slug]) }}">
                                    {{ route("front.child", [$post->child->faculty->slug, $post->child->slug], false) }}
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
        <div class="col-md-8">
            <!-- Horizontal Form -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4 class="box-title">Fotoğraflar</h4>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <div class="box-body">
                    <div class="post-images">
                        @include('admin.partials.media.post', [
                            'postMedias' => $post->media,
                            'childName' => $post->child->full_name
                        ])
                        <div class="img-add-container">
                            <a class="btn btn-app add-img-btn" post-id="{{ $post->id }}">
                                <i class="fa fa-plus"></i> Fotoğraf Eke
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- Horizontal Form -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h4 class="box-title">Metin</h4>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                {!! Form::model($post, ['method' => 'PUT', 'route' => ['admin.post.update', $post->id], 'class' => 'form-horizontal', 'files' => true]) !!}
                <div class="box-body">
                    {!! Form::textarea('text', null, ['class' => 'form-control summernote', 'required' => 'required']) !!}
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <a href="{{ route('admin.post.index') }}" class="btn btn-danger">Geri</a>
                    <div class="btn-group pull-right">
                        <button type="submit" class="btn btn-primary" name="approval" value="0">Kaydet</button>
                        <button type="submit" class="btn btn-success" name="approval" value="1">Kaydet & Onayla</button>
                    </div>
                </div>
                <!-- /.box-footer -->
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('admin.partials.modal.cropper')

    <script>
        deleteItem('media', 'isimli çocuğun fotoğrafını silmek istediğine emin misin?', 'delete-btn', '/admin/post/{{ $post->id }}/media/[ID]');
        featureItem('media', '', 'feature-btn', '/admin/post/{{ $post->id }}/media/[ID]/feature');
    </script>
    <script>
        @if($post->child->featured_media_id)
        setFeaturedMedia({{ $post->child->featured_media_id }});
        @endif
    </script>
@endsection

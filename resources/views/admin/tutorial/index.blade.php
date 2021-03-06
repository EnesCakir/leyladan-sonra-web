@extends('admin.layouts.app')

@section('title', 'Eğitimler')

@section('header')
    <section class="content-header">
        <h1>
            Eğitimler
            <small>{{ $tutorials->total() }} Video</small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="fa fa-home"></i> Anasayfa</a>
            </li>
            <li class="active">Eğitimler</li>
        </ol>
    </section>
@endsection

@section('content')
    <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
        <div class="input-group input-group-sm search-group">
            <input id="search-input" type="text" class="form-control table-search-bar pull-right search-input"
                   name="search" placeholder="Arama"
                   value="{{ request()->search }}">
            <div class="input-group-btn">
                <button id="search-btn" class="btn btn-default" type="submit">
                    <i class="fa fa-search"></i> Ara
                </button>
            </div>
        </div>
        <div class="btn-group btn-group-sm filter-group">
            {{-- TYPE SELECTOR --}}
            @include('admin.partials.selectors.default', [
              'selector' => [
                'id'        => 'category-selector',
                'class'     => 'btn-default',
                'icon'      => 'fa fa-files-o',
                'current'   => request()->category,
                'values'    => $categories,
                'default'   => 'Kategori',
                'parameter' => 'category'
              ]
            ])
            @can('create', App\Models\Tutorial::class)
                <a href="{{ route('admin.tutorial.create') }}" class="btn btn-success">
                    <i class="fa fa-plus"></i>
                </a>
            @endcan
        </div>
    </div>
    @forelse($tutorials->chunk(4) as $chunk)
        <div class="row">
            @foreach($chunk as $tutorial)
                <div id="tutorial-{{ $tutorial->id }}" class="col-sm-6 col-md-4">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title"> {{ $tutorial->name }}</h3> <span
                                    class="label label-danger label-sm">{{ $tutorial->category }}</span>
                            <div class="box-tools">
                                <div class="btn-group btn-group-xs">
                                    @can('update', $tutorial)
                                        <a class="edit btn btn-warning btn-xs"
                                           href="{{ route("admin.tutorial.edit", $tutorial->id) }}">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $tutorial)
                                        <a class="delete btn btn-danger btn-xs" delete-id="{{ $tutorial->id }}"
                                           delete-name="{{ $tutorial->name }}" href="javascript:;">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body no-padding text-center">
                            <div class="videoWrapper">
                                <iframe width="560" height="315" src="{{ $tutorial->link }}" frameborder="0"
                                        allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <div class="text-center">
            <p style="font-size: 80px; line-height: 1; margin: 10px;"><i class="fa fa-exclamation-triangle"></i></p>
            <p style="font-size: 24px;">Aradığınız kriterlerde veri bulunamadı</p>
        </div>
    @endforelse

    {{ $tutorials->appends([
      'search'   => request()->search,
      'category' => request()->category,
      'per_page' => request()->per_page,
    ])->links() }}

@endsection

@section('scripts')
    <script type="text/javascript">
        deleteItem("tutorial", "isimli eğitim videosunu silmek istediğinize emin misiniz?");
    </script>
@endsection
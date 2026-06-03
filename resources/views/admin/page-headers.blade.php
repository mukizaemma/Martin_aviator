@extends('layouts.adminbase')

@section('title', 'Page header images')

@section('content')
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">@include('admin.includes.sidenav')</div>
    <div id="layoutSidenav_content">
        <main class="ma-admin-page">
            <div class="container-fluid px-4 py-3">
                <div class="ma-page-head mb-4">
                    <h1 class="ma-page-title">Page header images</h1>
                    <p class="text-muted mb-0 small">Banner photos at the top of each public page. Recommended: wide landscape, at least 1600×600px. Images over 700 KB are compressed automatically on upload.</p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card ma-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Page</th>
                                        <th>Preview</th>
                                        <th>Upload / replace</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($headers as $header)
                                        @php
                                            $def = $definitions->get($header->slug);
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $header->label }}</strong>
                                                @if ($def)
                                                    <br><span class="small text-muted">{{ $def['path'] }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($header->image)
                                                    <img src="{{ $header->publicUrl() }}" alt="" class="rounded" style="max-height:72px; max-width:160px; object-fit:cover;">
                                                @else
                                                    <span class="text-muted small">No image — solid banner</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('pageHeaders.update', $header) }}" method="post" enctype="multipart/form-data" class="d-flex flex-wrap gap-2 align-items-center">
                                                    @csrf
                                                    <input type="file" name="image" class="form-control form-control-sm" accept="image/*" style="max-width:220px;">
                                                    <button type="submit" class="btn btn-ma-primary btn-sm">Save</button>
                                                </form>
                                            </td>
                                            <td class="text-end">
                                                @if ($header->image)
                                                    <form action="{{ route('pageHeaders.update', $header) }}" method="post" class="d-inline" onsubmit="return confirm('Remove this header image?');">
                                                        @csrf
                                                        <input type="hidden" name="remove_image" value="1">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">Remove</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Page header images</h1>
    <p class="text-muted">Banner photos at the top of each public page. Recommended: wide landscape, at least 1600×600px.</p>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
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
                                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
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
@endsection

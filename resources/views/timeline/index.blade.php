@extends('layouts.base')

@section('title', 'タイムライン（ダミー）')

@section('css')
    @vite('resources/css/pages/timeline.css')
@endsection

@section('content')
    <div class="timeline">
        @include('components.site-header')
    </div>
@endsection

//ユーザ一覧をを表示する
@extends('layouts.app')

@section('content')
    {{-- ユーザ一覧 --}}
    @include('users.users')
@endsection
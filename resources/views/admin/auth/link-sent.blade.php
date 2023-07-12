@extends('admin.layouts.default')

@section('page_title', trans('common.login') . config('default.page_title_delimiter') . config('default.app_name'))

@section('content')
    <div class="container h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="card shadow-sm bg-light my-3">
                    <div class="card-body p-5">
                        <div class="mb-4 text-center">
                            <svg width="6em" height="6em" viewBox="0 0 16 16" class="bi bi-person-circle" fill="#ccc"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M13.468 12.37C12.758 11.226 11.195 10 8 10s-4.757 1.225-5.468 2.37A6.987 6.987 0 0 0 8 15a6.987 6.987 0 0 0 5.468-2.63z" />
                                <path fill-rule="evenodd" d="M8 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                                <path fill-rule="evenodd"
                                    d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8z" />
                            </svg>
                        </div>
                        <div class="alert alert-success">
                            {!! trans('common.link_has_been_sent_to_email', ['email' => '<strong>' . $email . '</strong>']) !!}
                        </div>
                        <div class="mt-4">
                            <a href="{{ url('/' . app()->getLocale()) }}"
                                class="text-decoration-none text-muted">{{ trans('common.to_website') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('page_bottom')

@stop

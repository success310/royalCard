<form {!! $class ? 'class="' . $class . '"' : '' !!} @if ($action) action="{{ $action }}" @endif
    @if ($method) method="{{ $method }}" @endif {{ $attributes }}>
    @csrf

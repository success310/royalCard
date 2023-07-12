<div {!! $class ? 'class="' . $class . '"' : '' !!}>
    <button type="{{ $type }}" class="btn-primary btn-lg {{ $buttonClass }}">{!! $label !!}<span
            class="form-dirty hidden">&nbsp;*</span></button>
    @if ($back)
        <a href="{{ $backUrl }}" class="btn btn-link btn-block text-muted">{{ $backText }}</a>
    @endif
</div>

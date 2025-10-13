<div class="">
    @if($errors->any())
    {!! implode('', $errors->all('<div class="alert alert-danger"><i class="fas fa-exclamation-circle error-icons"></i> <strong>:message</strong> </div>')) !!}
    @endif


    @error('error')
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle error-icons"></i><strong>{{ $message }}</strong> </div>
    @enderror

    @error('success')
    <div class="alert alert-success"><i class="fad fa-check-double error-icons"></i><strong>{{ $message }}</strong> </div>
    @enderror

    @if(session()->has('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-double error-icons"></i><strong>{{ session()->get('success') }}</strong>
    </div>
    @endif

    @if(session()->has('message'))
    <div class="alert alert-primary">
        <strong>{{ session()->get('message') }}</strong>
    </div>
    @endif
</div>
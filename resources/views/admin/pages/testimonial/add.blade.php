@extends('admin.layout.default')

@section('testimonial_master', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">
                    <form method="POST" action="" class="w-100">
                        {{ csrf_field() }}
                        <div class="col-lg-9 col-xl-12">
                            <div class="row align-items-center">

                                <div class="form-group col-md-6">
                                    <label>Title</label>
                                    <div><input type="text" name="title" placeholder="Enter Testimonial Title"
                                            class="form-control" isrequired="isrequired" value="{{ old('title') }}">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>City</label>
                                    <div>
                                        <select name="city_id" id="cities" isrequired="isrequired"
                                            class="form-control selectpicker" data-live-search="true"
                                            data-dropup-auto="false" data-size="5">
                                            <option value="">--select city--</option>
                                            @foreach ($cities as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ request('city_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Locality</label>
                                    <div>
                                        <select name="locality_id" id="locality" isrequired="isrequired"
                                            class="form-control selectpicker" data-live-search="true"
                                            data-dropup-auto="false" data-size="5">
                                            <option value="">--select locality--</option>
                                            @foreach ($localities as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old('locality_id') == $item->id ? 'selected' : '' }}>
                                                    {{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Center</label>
                                    <div>
                                        <select name="centre_id" id="locality" isrequired="isrequired"
                                            class="form-control selectpicker" data-live-search="true"
                                            data-dropup-auto="false" data-size="5">
                                            <option value="">--select locality--</option>
                                            @foreach ($centeres as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old('centre_id') == $item->id ? 'selected' : '' }}>
                                                    {{ $item->centre_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label>content</label>
                                    <div>
                                        <textarea type="text" name="content" placeholder="Write Content.." rows="8" class="form-control"
                                        isrequired="isrequired">{{ old('content') }}</textarea>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Rating</label>
                                    <div class="rating">
                                        @for ($i=5; $i>=1;$i--)
                                         <input type="radio" id="star{{$i}}" name="rating" value="{{$i}}" required {{ $i == 4 ? 'checked' : ''}}><label
                                        for="star{{$i}}"></label>
                                        @endfor
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <div class="text-center"><button class="btn btn-success">Submit</button></div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Styles Section --}}
@section('styles')
    <link href="{{ asset('plugins/custom/select/css/bootstrap-select.min.css') }}" type="text/css" rel="stylesheet">
    <style>
        .rating {
            width: 220px;
            height: 58px;
            margin: 0 auto;
            padding: 5px;
            border: 1px solid #ccc;
            background: #f9f9f9;
        }

        .rating label {
            float: right;
            position: relative;
            width: 40px;
            height: 40px;
            cursor: pointer;
        }

        .rating label:not(:first-of-type) {
            padding-right: 2px;
        }

        .rating label:before {
            content: "\2605";
            font-size: 42px;
            color: #ccc;
            line-height: 1;
        }

        .rating input {
            display: none;
        }

        .rating input:checked~label:before,
        .rating:not(:checked)>label:hover:before,
        .rating:not(:checked)>label:hover~label:before {
            color: #f9df4a;
        }
    </style>
@endsection

{{-- Scripts Section --}}
@section('scripts')
    <link href="{{ asset('plugins/custom/select/js/bootstrap-select.min.js') }}" type="text/css" rel="stylesheet">
    <script>
        $('select').selectpicker();
        $('#cities').on('change',function(){
             window.location.href='add?city_id='+$(this).val();
        });

    </script>
@endsection

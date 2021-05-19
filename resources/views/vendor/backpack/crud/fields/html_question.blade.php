

<!-- text input -->
@include('crud::fields.inc.wrapper_start')
    <a href="#" id="form-question" value="{{ $field['value'] ?? '' }}" name="{{$field['name']}}">{!! $field['label'] !!}</a>
@include('crud::fields.inc.wrapper_end')

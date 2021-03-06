
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Create or Update a Resource")

@section('resources', 'active')



@section('content')


    @include('layouts.flashing')


    @if (isset($resource))
        <h2>Update Resource</h2>
        {!! Form::model( $resource, array('route' => array('resources.update', $resource->id), 'method' => 'put', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()') ) !!}
    @else
        <h2>Create Resource</h2>
        {!! Form::open(array('action' => 'Admin\ResourceController@store', 'id' => 'inputForm', 'oninput' => 'enableSubmitButton()') ) !!}
    @endif
        <p>{!! Form::label('name', 'Resource Name'); !!} <i class="red">*</i><br>
           {!! Form::text('name'); !!}</p>
        <p>{!! Form::label('type', 'Type information (optional'); !!}<br>
           {!! Form::text('type'); !!}</p>
        <p>{!! Form::label('details', 'Further details (optional'); !!}<br>
           {!! Form::text('details'); !!}</p>

    @if (isset($resource))
        <p>{!! Form::submit('Update', ['class'=>'btn btn-outline-success submit-button disabled']); !!}</p>
        <hr>
        <a class="btn btn-danger"  resource="button" href="{{ url('admin/resources/'.$resource->id) }}/delete">
            <i class="fa fa-trash" > </i> &nbsp; Delete
        </a>
    @else
        <p>{!! Form::submit('Submit', ['class'=>'btn btn-outline-success submit-button disabled']); !!}
    @endif

    <script type="text/javascript">document.forms.inputForm.name.focus()</script>

    <a href="{{ url('admin/resources/') }}">{!! Form::button('Cancel', ['class'=>'btn btn-secondary cancel-button']); !!}</a></p>
    {!! Form::close() !!}
    
    <span><i class="red">*</i> = mandatory field(s) &nbsp;</span>
    
@stop
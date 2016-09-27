
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', $heading)

@section('types', 'active')



@section('content')


    @include('layouts.flashing')

    @if(Auth::user()->isEditor())
    <a class="btn btn-outline-primary pull-xs-right" href="{{ url('admin/types/create') }}">
        <i class="fa fa-plus"> </i> &nbsp; Add new type
    </a>
    @endif

    <h2>
        {{ $heading }}
        <small class="text-muted">
            <a tabindex="0" href="#"
                data-container="body" data-toggle="popover" data-placement="bottom" data-trigger="focus" data-title="Service Types"
                data-content="... determine the title (name) of services and which standard items can be inserted for new plans.">
                <i class="fa fa-question-circle"></i></a>
        </small>
    </h2>


    @if (count($types))

        <table
            class="table table-striped table-bordered 
                    @if(count($types)>5)
                     table-sm
                    @endif
                     ">
            <thead class="thead-default">
                <tr>
                    <th class="center hidden-xs-down">#</th>
                    <th class="center">Name</th>
                    <th class="center hidden-lg-down">Subtitle/Location</th>
                    <th class="center hidden-md-down" colspan="2">Usual Begin and End</th>
                    <th class="center hidden-lg-up">Begin-End</th>
                    <th class="center small">Default Weekday</th>
                    <th class="center">Interval</th>
                    <th class="center hidden-sm-down small">Default Leader</th>
                    <th class="center hidden-sm-down small">Default Resource</th>
                    <th class="center small">Default Items</th>
                    <th class="center small">Total No. of Plans</th>
                    @if( Auth::user()->id===1 || Auth::user()->isAdmin() )
                        <th class="center">Show/Modify</th>
                    @endif
                </tr>
            </thead>

            <tbody>

            @foreach( $types as $type )
                <tr>

                    <td class="center hidden-xs-down" scope="row">{{ $type->id }}</td>

                    <?php   $tdl = 'link center" title="Click to edit" onclick="location.href='."'".url('admin/types/'.$type->id).'/edit'."'"; ?>

                    <td class="{!! $tdl !!}">{{ $type->name }}</td>

                    <td class="hidden-lg-down {!! $tdl !!}">{{ $type->subtitle }}</td>

                    <td class="hidden-md-down {!! $tdl !!}">{{ substr($type->start,0,5) }}</td>
                    <td class="hidden-md-down {!! $tdl !!}">{{ substr($type->end,0,5) }}</td>
                    <td class="hidden-lg-up {!! $tdl !!}">{{ substr($type->start,0,5).'-'.substr($type->end,0,5) }}</td>

                    <td class="{!! $tdl !!}">{{ $type->weekdayName }}</td>

                    <td class="{!! $tdl !!}">{{ $type->repeat }}</td>

                    <td class="hidden-sm-down {!! $tdl !!}">{{ $type->default_leader ? $type->default_leader->name : '' }}</td>

                    <td class="hidden-sm-down {!! $tdl !!}">{{ $type->default_resource ? $type->default_resource->name : '' }}</td>

                    <td class="link center" onclick="location.href='{{ url('admin/default_items?filterby=type&filtervalue='.$type->id) }}'" 
                        title="Show Default Items for this Event Type">{{ $type->defaultItems->count() }} <sup><small><small><i class="text-muted fa fa-search"></i></small></small></sup></td>

                    <td class="link center" onclick="location.href='{{ url('cspot/plans?filterby=type&filtervalue='.$type->id) }}&show=all'" 
                        title="Show all Plans of this Type of Service">{{ $type->plans->count() }} <sup><small><small><i class="text-muted fa fa-search"></i></small></small></sup></td>


                    <td class="nowrap center">
                        <a class="btn btn-secondary btn-sm" title="Show all future Plans" href='{{ url('cspot/plans?show=future&filterby=type&filtervalue='.$type->id ) }}'><i class="fa fa-filter"></i></a>

                         @if( Auth::user()->isEditor() )
                            <a class="btn btn-outline-primary btn-sm pull-xs-right" title="Edit" 
                                href='{{ url('admin/types/'.$type->id) }}/edit'>
                                    <i class="fa fa-pencil"></i></a>
                            @if (! $type->plans->count())
                                <a class="btn btn-danger btn-sm pull-xs-right" title="Delete!" 
                                    href='{{ url('admin/types/'.$type->id) }}/delete'>
                                        <i class="fa fa-trash"></i></a>
                            @endif
                        @endif
                    </td>

                </tr>
            @endforeach

            </tbody>

        </table>

    @else

        No types found!

    @endif

    
@stop

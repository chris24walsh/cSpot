
<!-- # (C) 2016 Matthias Kuhs, Ireland -->

@extends('layouts.main')

@section('title', "Show Chords")

@section('plans', 'active')



@section('content')


    @include('layouts.flashing')




    {{-- ================================================================================ --}}
    <div id="main-content">
    {{-- ================================================================================ --}}


        @if ($item->song_id )

            @if ($item->key)
                <h4 class="red">{{ $item->key }}</h4>
            @endif

            @if ( isset($onSongChords)  &&  $onSongChords->count() )
                @include ('cspot.snippets.present_chords')

            @elseif ($item->song->chords )
                <div class="mb-3">
                    <pre class="text-song" id="chords">{{ $item->song->chords }}</pre>
                </div>

            @else
                {{ $item->song->title_2=='slides' ? '(Info slide)' : '(No chords available!)' }}
                @if ( count($item->song->files)>0 )
                    <div class="mb-3">
                        @foreach ($item->song->files as $file)
                            <img class="figure-img img-fluid img-rounded"  
                                src="{{ url(config('files.uploads.webpath')).'/'.$file->token }}">
                        @endforeach
                    </div>
                @elseif ($item->song->title_2 != 'video')
                    <pre class="text-song mb-3" id="lyrics">{{ $item->song->onsongs ? $item->song->onSongLyrics() : $item->song->lyrics }}</pre>
                @endif
            @endif

                
            <div class="hidden-xs-up" id="sequence">{{ $item->song->sequence }}</div>



        
        @elseif ($item->files)
            @foreach ($item->files as $file)
                <figure class="figure">
                    <img class="figure-img img-fluid img-rounded full-width" 
                           src="{{ url(config('files.uploads.webpath')).'/thumb-'.$file->token }}">
                </figure>
            @endforeach
        @endif




        @if ($bibleTexts)
            <div class="col-xl-6" id="bibletext">
                @foreach ($bibleTexts as $btext)
                    <h3>{{ $btext->display }} ({{ $btext->version_abbreviation }})</h3>
                    @if (isset($verses)  && count($verses))
                        @php
                            if (gettype($verses)=='array')
                                $versesPart = $verses[ $loop->index ];
                            else
                                $versesPart = $verses;
                        @endphp
                        @foreach ($versesPart as $verse)
                            <p class="p mb-0">
                                <sup class="v">{{ $verse->verse }}</sup> 
                                <span>{{ $verse->text }}</span>
                            </p>
                        @endforeach
                    @else 
                        <div>{!! $btext->text !!}</div>
                    @endif
                    <div class="small">{!! $btext->copyright !!}</div>
                    <hr>
                @endforeach
            </div>

        @else
            <div class="jumbotron" id="item-comment">
                <h1 class="display-3 center">
                    <span class="text-muted">{{ $item->comment ? '('.$item->comment.')' : '' }}</span>
                </h1>
            </div>
        @endif


    </div>
    <!-- ================================================================================ -->



    @include('cspot.snippets.present_navbar')



@stop
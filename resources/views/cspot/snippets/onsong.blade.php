
{{-- 
	code to show the OnSong parts of a song
	and to provide the necessary tools tomodify same (add, edit, delete) 
--}}


<div  class="table table-striped table-sm"
		id="onsong-parts" 
		data-song-id="{{ isset($song) ? $song->id : '0' }}" 
		data-update-onsong-url="{{ route('updateonsongparts') }}">


    @if ( isset($song) )
		@foreach ($song->onsongs as $onsong)
			<div class="row border border-top-0{{ $onsong->song_part->code=='m' ? ' onsong-meta-data' : '' }}"
			 	 id="tbl-row-{{ $onsong->id }}" 
				 data-onsong-id="{{ $onsong->id }}" data-part-id="{{ $onsong->song_part_id }}">

				{{-- show the part name and code above the onsong data on smaller devices 
				--}}
				<div class="col col-sm-12 col-md-3 col-xl-2">

					{{ $onsong->song_part->code!='m' ? $onsong->song_part->name : '' }}
					<span>{{ $onsong->song_part->code!='m' ? '('.$onsong->song_part->code.')' : '' }}</span>

				</div>
				<div class="col col-sm-12 col-md-9 col-xl-10">

					@include('cspot.snippets.onsong_action', ['newOnsongRow' => false])

				</div>

			</div>
		@endforeach
		<script>
			// move the metadata row to the top of the table rows
			row = $(".onsong-meta-data");
			row.insertBefore(row.prevAll().last());
		</script>
	@endif


	{{-- row to enter new song parts - invisible at first
	--}}
	<div class="row" style="display: none;" id="new-onsong-row">
		<div class="col col-sm-12 col-md-3 col-xl-2 cell-part-name text-xs-center">

			<select class="new-onsong-field" tabindex=1>
				<option value="">Select ...</option>
				@foreach ($songParts as $part)
					<option value="{{ $part->id }}">{{ $part->name }}</option>
				@endforeach
			</select>
			<div class="error-msg hidden">Select a name!</div>
			<br>
			<a href="{{ url('admin/song_parts') }}" target="new" class="small">edit list <i class="fa fa-external-link"></i></a>
			<span class="cell-part-code hidden-md-down"></span>

		</div>
		<div class="col col-sm-12 col-md-9 col-xl-10">

			@include('cspot.snippets.onsong_action', ['newOnsongRow' => true])

		</div>
	</div>



	<div class="row bg-faded link insertNewOnSongRow-link">
		<div class="col col-xs-12 pl-2">
			@if (Auth::user()->isEditor())
				<span onclick="insertNewOnSongRow();" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Add new Part</span>
			@endif
			<span class="small float-right">
				<a href="http://www.logue.net/xp/" target="new"><span class="text-info">Tool for Transposing</span>
					<i class="fa fa-external-link"></i></a>
			</span>
		</div>
	</div>


	<div class="row show-onsong-format-hint text-center hidden">
		<div class="col col-xs-12 small">
			<p class="card">
				If the imported text contains tab-stopps, define how many spaces they should be replaced with:
				<input type="number" id="onsong-import-tab-size" value=4 style="width: 2rem" onchange="updateTabToSpacesRatio(this)">
			</p>
		</div>
	</div>




</div>
<a id="tbl-bottom"></a>
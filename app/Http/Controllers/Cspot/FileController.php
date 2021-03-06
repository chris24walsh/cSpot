<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Cspot;

use Illuminate\Http\Request;

use Snap\BibleBooks\BibleBooks;

use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Models\Item;
use App\Models\File;
use App\Models\FileCategory;

use Auth;



class FileController extends Controller
{


    /**
     * Authentication
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('role:leader', ['except' => ['APIindex', 'add', 'update', 'upload'] ]);
    }




    /**
     * FILES/IMAGES HANDLING
     *
     */
    public function index(Request $request)
    {
        $heading = 'List All Files and Images';

        $querystringArray = $request->input();

        # does session contains a filter value?
        if ($request->has('bycategory')) {
            // get only FILES with this specific file_category id
            $file_category = FileCategory::find($request->input('bycategory'));
            if ($file_category) {
                $heading = 'Show Files of type "'.$file_category->name.'"';
                // get all files of this category
                $files = $file_category->files()->paginate(18)->appends($querystringArray);
            }
        }
        if ($request->has('newest')) {
            $heading = 'Recently added files';
            $files = File::orderBy('id', 'desc')->paginate(18);
        }
        // default: show all files
        if (! isset($files)) {
            $files = File::paginate(18);
        }

        // URL contains ...?item_id=xxx (needed in order to add an existing file to an item)
        $item_id = 0;
        if ($request->has('item_id')) {
            $item_id = $request->item_id;
            $heading = 'Select a file for the Plan Item';
        }

        // get list of file categories
        $file_categories = FileCategory::get();

        // for pagination, always append the original query string
        $files = $files->appends($querystringArray);

        return view('admin.files', [
            'files'           => $files, 
            'item_id'         => $item_id, 
            'heading'         => $heading,
            'file_categories' => $file_categories
        ]);
    }



    /**
     * API - get list of files, optionally filtered by category
     */
    public function APIindex($category=null)
    {
        // get all files of this category
        if ($category) {

            if ($category == 'newest') {
                // Recently added files
                $files = File::orderBy('id', 'desc')->paginate(18);
            } 
            else {
                $files = File::where('file_category_id', $category)->paginate(18);
            }
        }
        // get all files
        else {
            $files = File::paginate(18);
        }

        return response()->json( $files );
    }



    /**
     * Update information about an existing file
     */
    public function update(Request $request)
    {
        if (! $request->has('id'))
            return;
        $file = File::find($request->id);
        if ($file) {
            $file->filename = $request->filename;
            $file->file_category_id = $request->file_category_id;
            $file->save();
            return response()->json(['status' => 200, 'data' => $file ]);
            //return addslashes( json_encode($file, JSON_HEX_APOS | JSON_HEX_QUOT) );
        }
        return 'file not found!';
    }




    /**
     * Add existing file to a plan item
     */
    public function add(Request $request, $item_id=null, $file_id=null)
    {
        if ($request->has('item_id'))
            $item_id = $request->item_id;
        if ($request->has('file_id'))
            $file_id = $request->file_id;

        $item = Item::with('files')->find($item_id);

        if ($item) {

            // find the new seq no
            $new_seq_no = getLatestSeqNoOfFilesAttachedToItem($item) + 1;

            $item->files()->attach($file_id, ['seq_no' => $new_seq_no]);
            
            $file = File::find($file_id);

            // notify the view about the newly added file
            $request->session()->flash('newFileAdded', $file->id);

            if ($request->is('*/api/*')) { 
                return response()->json(['status' => 200, 'data' => $file]); 
            }

            return \Redirect::route( 'cspot.items.edit', [$item->plan_id, $item->id] );
        }

        if ($request->is('*/api/*')) { 
            return response()->json(['status' => 404, 'data' => 'Not found'], 404); 
        }

        flash('Error! Item with ID "' . $id . '" not found');
        return \Redirect::back();
    }



    /**
     * Upload single file via AJAX
     */
    public function upload(Request $request)
    {
        // check if a valid file was submitted
        if (   $request->hasFile('file') 
            && $request->file('file')->isValid()
            && $request->has('file_category_id')  )
        {
            // use the helper function
            $file = saveUploadedFile($request);
            $file->save();
            return response()->json(['status' => 200, 'data' => $file]); 
        }
        return response()->json(['status' => 404, 'data' => 'Incomplete request'], 404); 
    }



    /**
     * Remove a file attachment
     *
     * - - RESTful API request - -
     *
     * @param int $id
     *
     */
    public function delete($id)
    {
        // find the single resource
        $file = File::find($id);
        if ($file) {
            // check authentication
            if (! Auth::user()->isAdmin() ) {
                return response()->json(['status' => 401, 'data' => 'Not authorized'], 401);
            }
            $destinationPath = config('files.uploads.webpath');
            // check if file actually exists
            if ( file_exists(public_path().'/'.$destinationPath.'/'.$file->token)) {
                // delete the physical file
                unlink(public_path().'/'.$destinationPath.'/'.$file->token);
            }
            // also delete possible thumbnail files
            deleteThumbs(public_path().'/'.$destinationPath, $file->token);
            
            // delete the database record
            $file->delete();
            // return to sender
            return response()->json(['status' => 200, 'data' => 'File "'.$file->filename.'" was deleted.']);
        }
        return response()->json(['status' => 404, 'data' => 'Not found'], 404);
    }






    /**
     * Unlink a file attachment
     *
     * - - RESTful API request - -
     *
     * @param int $id
     *
     */
    public function APIunlink(Request $request)
    {
        $item_id = $request->has('item_id') ? $request->item_id : 0;
        $file_id = $request->has('file_id') ? $request->file_id : 0;

        if (!$item_id || !$file_id)
            return response()->json(['status' => 404, 'data' => 'API: songId or fileId missing!'], 404);
        
        // find the single resource
        $item = Item::with('files')->find($item_id);

        if ($item) {

            $item->files()->detach($file_id);

            correctFileSequence($item);

            // return to sender
            return response()->json(['status' => 200, 'data' => 'File unlinked.']);            
        }

        return response()->json(['status' => 404, 'data' => 'Item with id '.$item_id.' not found!'], 404);
    }




    /**
     * Change seq_no of a file 
     */
    public function move(Request $request, $item_id, $file_id, $direction)
    {
        $item = Item::with('files')->find($item_id);
        if ($item) {
            foreach ($item->files as $file) {

                if ($file->id == $file_id) {
                    $k = $file->pivot->seq_no;
                    if ($direction=='up') {
                        $file->pivot->update(['seq_no' => $k - 1.1]);
                    }
                    if ($direction=='down') {
                        $file->pivot->update(['seq_no' => $k + 1.1]);
                    }
                }
            }
            $item->save();

            // make sure all files atteched to this item have the correct seq no now
            correctFileSequence($item);

            // notify view that the image was moved
            $request->session()->flash('newFileAdded', $file->id);

            return \Redirect::route( 'cspot.items.edit', [$item->plan_id, $item->id] );
        } 
        else {
            flash('Error! Item with ID "' . $item_id . '" not found');
        }

        return \Redirect::back();
    }


}


<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CKEditorController extends Controller
{
    public function upload(Request $request)
    {
        if($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName.'_'.time().'.'.$extension;
        
            $request->file('upload')->move(public_path('/uploads'), $fileName);
 
            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = asset('/uploads/'.$fileName); 
            $msg = 'Gambar berhasil di upload.'; 
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
                
            @header('Content-type: text/html; charset=utf-8'); 
            echo $response;
        }
    }

    public function delete($id)
    {
        $soal = Soal::find($id);

        if ($soal) {
            
            $soal->status = 'delete';
            $soal->save();

            $imagePath = public_path('uploads/' . $soal->image);

            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }

            return response()->json(['success' => 'Soal berhasil dihapus.']);
        }

        return response()->json(['error' => 'Soal not found.'], 404);
    }
}
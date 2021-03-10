<?php

namespace App\Modules\Files\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Files\Repositories\FilesRepository;

class FilesController extends Controller
{
    protected $repo;
    public function __construct()
    {
        $this->repo = new FilesRepository;
    }
    public function add(Request $request){
        if (!$request->files){
            return response()->json(['Вы не выбрали файл']);
        }
        $validator = \Validator::make($request->all(), ['photo'=>'required|image']);
        if ($validator->fails()){
            return response()->json($validator->errors());
        }

        $file_id = $this->repo->upload_file($request->file('photo'), $request->folder);

        return response()->json(['file_id'=>$file_id, 'name'=>$request->name]);
    }

}

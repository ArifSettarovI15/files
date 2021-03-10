<?php


namespace App\Modules\Files\Repositories;

use App\Modules\Files\Models\FileModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
class FilesRepository
{

    public static function getFile($id){

        $file = FileModel::find($id);
        if ($file){
           return Storage::url($file->file_folder.'/'.'medium/'.$file->file_name);
        }
        return Storage::url('core/'.'no-photo.png');
    }
    public  function upload_file($file_data, $folder='core'){

        $saved_sizes = [];
        $filename=Str::random(10).'.'.$file_data->extension();

        $img = Image::make($file_data);
        $sizes = $this->GetImageSizes();

        foreach ($sizes as $size_folder=>$size){
            $file_data->storeAs($folder.'/'.$size_folder, $filename);

            $source = storage_path().'/app/'.$folder.'/'.$size_folder.'/'.$filename;

            $image = Image::make($source);

            if (($img->width() / $size['width']) > ($img->height() / $size['height'])) {
                if ($img->width()  < $size['width']) {
                    $size['width'] = $img->width() ;
                }
                $image->widen($size['width']);
            } else {
                if ($img->height() < $size['height']) {
                    $size['height'] = $img->width();
                }
                $image->heighten($size['height']);
            }
            $image->save($source);
            $saved_sizes[] = $size_folder;

        }
        $file = new FileModel;
        $file->file_name = $filename;
        $file->file_module = $folder;
        $file->file_folder = $folder;
        $file->file_sizes = serialize($saved_sizes);
        $file->file_type = $file_data->getMimeType();
        $file->file_time = time();
        if (Auth::id())
            $file->file_user_id = Auth::id();
        $file->save();

        return $file->file_id;
    }

    function GetImageSizes () {
        $images_sizes=array(
            'medium' => array(
                'width' => 600,
                'height' => 600
            ),
            'normal' => array(
                'width' => 800,
                'height' => 800
            ),
            'small' => array(
                'width' => 300,
                'height' => 300
            ),
            'large'=>array(
                'width' => 1200,
                'height' => 900
            ),
            'original'=>array(
                'width' => 1920,
                'height' => 1920
            )
        );
        return $images_sizes;
    }
}

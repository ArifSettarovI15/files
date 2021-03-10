<?php


namespace App\Modules\Files\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * @method static create()
 * @method static find($id)
 */
class FileModel extends Model
{
    protected $table = 'core_files';
    protected $primaryKey = "file_id";


    public $timestamps = false;
    protected $fillable = [
        'file_title',
        'file_name',
        'file_module',
        'file_user_id',
        'file_type',
        'file_folder',
        'file_sizes',
        'file_time',
        ];
}

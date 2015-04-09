<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model{
    protected $fillable = array('from_id','from_name','thread_id','time','message','data','other','tags');
    protected $table = 'chat_messages';

}
<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model{
    protected $fillable = array('from_id','from_name','thread_id','time','message','data_shares','other','tags','time_string','time_stamp','time','data_attachments');
    protected $table = 'chat_messages';

}
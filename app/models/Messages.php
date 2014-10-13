<?php
class Messages extends Eloquent{
    protected $fillable = array('from_id','from_name','thread_id','time','message','data','other');
    protected $table = 'chat_messages';

}
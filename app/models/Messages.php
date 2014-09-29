<?php
class Messages extends Eloquent{
    protected $fillable = array('from_id','from_name','thread_id','time','message');
    protected $table = 'chat_messages';

}
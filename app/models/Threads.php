<?php
class Threads extends Eloquent{
    protected $fillable = array('thread_id','message_count','participants_names','participants_ids'); // need to read up on mass assignment/ security issues

} 
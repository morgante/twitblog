<?php

class TwitBlog extends Plugin
{
	
	public function action_update_check()
	{
		Update::add( $this->info->name, '2df6cef0-3986-4e59-b9ca-6d76e7a85dd3', $this->info->version );
	}
	
	/**
	 * Link usernames in microposts to twitter accounts 
	 **/
	public function filter_microblog_userlink( $array, $micropost )
	{
		$handled = $array[0];
		$username = $array[1];
		
		if( !$handled )
		{
			return array( true, 'http://twitter.com/' . $username) ;
		}
		
		return $array;
	}	
}

?>
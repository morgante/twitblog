<?php

class TwitBlog extends Plugin
{
	
	public function action_update_check()
	{
		Update::add( $this->info->name, '2df6cef0-3986-4e59-b9ca-6d76e7a85dd3', $this->info->version );
	}
	
	/**
	 * Add twittername to user options
	 */
	public function action_form_user($form, $edit_user)
	{
		$userid = $form->user_info->append( 'text', 'twittername', 'null:null', _t( 'Twitter Username'), 'optionscontrol_text' );
		$userid->class[] = 'item clear';
		$userid->value = $edit_user->info->twitter__name;
	}
	
	/**
	 * set twittername user option
	 */
	public function filter_form_user_update($update, $form, $edit_user)
	{
		if($form->twittername->value != $edit_user->info->twitter__name)
		{
			$edit_user->info->twitter__name = $form->twittername->value;
			return true;
		}
		return $update;
	}
	
	/**
	 * Allow twitter send service
	 **/
	public function filter_microblog__send_services( $services )
	{
		$services['twitter'] = false;
		return $services;
	}
	
	/**
	 * Allow twitter link service
	 **/
	public function filter_microblog__link_services( $services )
	{
		$services['twitter'] = false;
		return $services;
	}
	
	/**
	 * Allow twitter copy service
	 **/
	public function filter_microblog__copy_services( $services )
	{
		$services['twitter'] = false;
		return $services;
	}
	
	/**
	 * Add our handlers for twitter
	 **/
	public function filter_microblog__servicehandlers( $handlers )
	{
		$handlers['twitter'] = array(
			'send' => array( $this, 'send_post'),
			'name' => array( $this, 'service_name'),
			'link' => array( $this, 'linkify'),
			'copy' => array( $this, 'copy_post')
		);
		
		return $handlers;
	}
	
	/**
	 * Provide the name for Twitter service
	 */
	public function service_name()
	{
		return 'Twitter';
	}
	
	
	/**
	 * Provide the name for Twitter service
	 */
	public function linkify( $type, $given )
	{
		switch( $type )
		{
			case 'user':
				return 'http://twitter.com/' . $given; // @TODO: make this check if that user actually exists
		}
	}
	
	/**
	 * Send a micropost to Twitter
	 */
	public function send_post( $post, $user )
	{
				
		require_once dirname(__FILE__) . '/../twitter/lib/twitteroauth/twitteroauth.php';
		
		$oauth = new TwitterOAuth(Twitter::CONSUMER_KEY_WRITE, Twitter::CONSUMER_SECRET_WRITE, $user->info->twitter__access_token, $user->info->twitter__access_token_secret);
		
		$oauth->post('statuses/update', array('status' => $post->content));
		
		Session::notice(_t('Post Tweeted', 'twitter'));
		
		// exit;

	}
	
	public function copy_post( $user )
	{
		$class = new Twitter;
		
		$username = $user->info->twitter__name;
		
		$tweets = $class->tweets( $username, false, 5, 0, false );
				
		if( !isset( $tweets[0]->id ) ) // no tweets available
		{
			return array();
		}
		
		return $tweets;
	}
	
}

?>
<?php

namespace App\Services\Twitter;


use Codebird\Codebird;
use App\Services\Twitter\Exceptions\RateLimitExceededException;

class CodeBirdTwitterService implements TwitterService
{
	protected $client;

	public function __construct(Codebird $client)
	{
		$this->client = $client;
	}
	public function getMentions($since = null)
	{
		
		$mentions = $this->client->statuses_mentionsTimeline( $since ? 'since_id=' . $since : '');

		if( (int) $mentions->rate->remaining === 0){
			throw new RateLimitExceededException;
		}
		return collect($this->extractTweets($mentions));
	}
	public function sendTweet($text, $inReplyTo = null)
	{
		$params = [
		'status'=>$text,

		];

		if($inReplyTo){
			$params['in_reply_to_status_id']=$inReplyTo;
		}

		$this->client->statuses_update($params);
	}
	protected function extractTweets($response)
	{
		unset($response->rate);
		unset($response->httpstatus);

		return $response;
	}
}
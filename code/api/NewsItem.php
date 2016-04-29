<?php
/**
 * @package BraftonApi
 */

namespace brafton;
use Exception;
/**
 * Constant Definitions for XML elements and attributes
 */
define("BRAFTON_NEWS_LIST_ITEM", "newsListItem");
define("BRAFTON_NEWS_ITEM", "newsItem");
define("BRAFTON_HREF", "href");
define("BRAFTON_ID", "id");
define("BRAFTON_HEADLINE", "headline");
define("BRAFTON_PUBLISH_Date", "publishDate");
define("BRAFTON_ENCODING", "encoding");
define("BRAFTON_CREATED_DATE", "createdDate");
define("BRAFTON_LAST_MODIFIED_DATE", "lastModifiedDate");
define("BRAFTON_EXTRACT", "extract");
define("BRAFTON_TEXT", "text");
define("BRAFTON_BY_LINE", "byline");
define("BRAFTON_TWEET_TEXT", "tweetText");
define("BRAFTON_SOURCE", "source");
define("BRAFTON_STATE", "state");
define("BRAFTON_CLIENT_QUOTE", "clientQuote");
define("BRAFTON_HTML_TITLE", "htmlTitle");
define("BRAFTON_HTML_META_DESCRIPTION", "htmlMetaDescription");
define("BRAFTON_HTML_META_KEYWORDS", "htmlMetaKeywords");
define("BRAFTON_HTML_META_LANGUAGE", "htmlMetaLanguage");
define("BRAFTON_TAGS", "tags");
define("BRAFTON_PRIORITY", "priority");
define("BRAFTON_FORMAT", "format");
define("BRAFTON_PHOTOS", "photos");
define("BRAFTON_CATEGORIES", "categories");
define("BRAFTON_COMMENTS", "comments");
/**
 * class NewsItem models a news object and has a static method to parse 
 * a set of news items and return them as a collection of NewsItem objects
 * @package BraftonApi
 */
class NewsItem extends \Object {
	/* @var XMLHandler */
	private $xh;
	/* @var String */
	private $encoding;
	/* @var int */
	private $id;
	/* @var String */
	private $publishDate;
	/* @var String */
	private $createdDate;
	/* @var String */
	private $lastModifiedDate;
	/*  @var String */
	private $headline;
	/* @var String */
	private $extract;
	/* @var String */
	private $text;
	/* @var String */
	private $href;
	/* @var String */
	private $byLine;
	/* @var String */
	private $tweetText;
	/* @var String */
	private $source;
	/* @var String */
	private $state;
	/* @var String */
	private $clientQuote;
	/* @var String */
	private $htmlTitle;
	/* @var String */
	private $htmlMetaDescription;
	/* @var String */
	private $htmlMetaKeywords;
	/* @var String */
	private $htmlMetaLanguage;
	/* @var String */
	private $tags;
	/* @var int */
	private $priority;
	/* @var String */
	private $format;
	/* @var photos[] */
	private $photos;
	/* @var NewsCategory[] */
	private $categories;
	/* @var NewsComment[] */
	private $comments;

	/** @return NewsItem **/
	function __construct(){
	}

	/** @return XMLHandler **/
	private function getFullNewsXML(){
		if(empty($this->xh)){
			if(strcasecmp($this->getFormat(), "html"))$this->xh = new XMLHandler($this->href);
			else $this->xh = new XMLHandler($this->href . $this->getFormat());
		}
		return $this->xh;
	}

	/**
	 * @param String $url
	 * @return NewsItem[]
	 */
	public static function getNewsList($url, $format) {
		//Exception thrown in XMLHandler constructor if url is incorrect	
		$xh = new XMLHandler($url);

		$newsList = array();
		if(isset($xh)){
			$news = $xh->getNodes(BRAFTON_NEWS_LIST_ITEM);
			$exceptionList = array();

			foreach($news as $n){
				/* @var $n DomElement */
				$ni = new NewsItem();
				try{
					//Check if all required nodes exist, throw exception if not!
					if($n->getElementsByTagName(BRAFTON_ID)->length==0)throw new XMLNodeException("Element " . BRAFTON_ID . " for " . BRAFTON_NEWS_LIST_ITEM);
					//set value of BRAFTON_ID here to use in debugging!
					$ni->id = $n->getElementsByTagName(BRAFTON_ID)->item(0)->textContent;
						
					if($n->getElementsByTagName(BRAFTON_PUBLISH_Date)->length==0)throw new XMLNodeException("Element " . BRAFTON_PUBLISH_Date . " for " . BRAFTON_NEWS_LIST_ITEM . " with id: " . $ni->id);
					if(!$n->getAttribute(BRAFTON_HREF))throw new XMLNodeException("Attribute " . BRAFTON_HREF . " for " . BRAFTON_NEWS_LIST_ITEM . " with id: " . $ni->id);
					if($n->getElementsByTagName(BRAFTON_HEADLINE)->length==0)throw new XMLNodeException("Element " . BRAFTON_HEADLINE . " for " . BRAFTON_NEWS_LIST_ITEM . " with id: " . $ni->id);

					//Check if date is valid if not throw exception
					$ni->publishDate = $n->getElementsByTagName(BRAFTON_PUBLISH_Date)->item(0)->textContent;
					$dateIsValid = date_parse($ni->publishDate);
					if(!$dateIsValid)throw new DateParseException("Invalid Date for " . BRAFTON_PUBLISH_Date . "  on " . BRAFTON_NEWS_LIST_ITEM . " with id: " . $ni->id . "<br />\n");

					//Set the value of all other required elements
					$ni->href = $n->getAttribute(BRAFTON_HREF);
					$ni->headline = $n->getElementsByTagName(BRAFTON_HEADLINE)->item(0)->textContent;
					$ni->format = $format;
						
					//Add to newslist array
					$newsList[] = $ni;
				}
				catch(XMLException $e){
					$exceptionList[] = $e; //Add exception to a list
				}
				catch(DateParseException $e){
					$exceptionList[] = $e;
				}
			}
			//If exception list contains any exceptions throw a new exception which relays all exceptions to the user
			if(!empty($exceptionList)){
				echo implode("<br />", $exceptionList) . "<br /><br />";
			}
		}
		return $newsList;
	}

	/** @return String **/
	public function getEncoding() {
		if(empty($this->encoding)){
			$xh = $this->getFullNewsXML();
			$this->encoding = $xh->getAttributeValue(BRAFTON_NEWS_ITEM, BRAFTON_ENCODING);
		}
		return $this->encoding;
	}

	/** @return int **/
	public function getId() {
		if(empty($this->id)){
			$xh = $this->getFullNewsXML();
			$this->id = $xh->getValue(BRAFTON_ID);
		}
		return $this->id;
	}

	/** @return String **/
	public function getPublishDate() {
		if(empty($this->publishDate)){
			$xh = $this->getFullNewsXML();
			$this->publishDate = $xh->getValue(BRAFTON_PUBLISH_Date);
		}
		return $this->publishDate;
	}

	/** @return String **/
	public function getHeadline() {
		if(empty($this->headline)){
			$xh = $this->getFullNewsXML();
			$this->headline = $xh->getValue(BRAFTON_HEADLINE);
		}
		return $this->headline;
	}

	/** @return String **/
	public function getCategories() {
		if(empty($this->categories)){
			$xh = $this->getFullNewsXML();
			$this->categories = NewsCategory::getCategories($xh->getHrefValue(BRAFTON_CATEGORIES));
		}
		return $this->categories;
	}

	/** @return String **/
	public function getCreatedDate() {
		if(empty($this->createdDate)){
			$xh = $this->getFullNewsXML();
			$this->createdDate = $xh->getValue(BRAFTON_CREATED_DATE);
			if(empty($this->createdDate))throw new XMLNodeException("Element " . BRAFTON_CREATED_DATE . " for " . BRAFTON_NEWS_LIST_ITEM . " with id: " . $this->id . "<br />\n");
		}
		return $this->createdDate;
	}

	/** @return String **/
	public function getLastModifiedDate() {
		if(empty($this->lastModifiedDate)){
			$xh = $this->getFullNewsXML();
			$this->lastModifiedDate = $xh->getValue(BRAFTON_LAST_MODIFIED_DATE);
			if(empty($this->lastModifiedDate))throw new XMLNodeException("Element " . BRAFTON_LAST_MODIFIED_DATE . " for " . BRAFTON_NEWS_LIST_ITEM . " with id: " . $this->id . "<br />\n");
		}
		return $this->lastModifiedDate;
	}

	/** @return String **/
	public function getPhotos() {
		if(empty($this->photos)){
			$xh = $this->getFullNewsXML();
			$this->photos = Photo::getPhotos($xh->getHrefValue(BRAFTON_PHOTOS));
		}
		return $this->photos;
	}

	/** @return String **/
	public function getComments() {
		if(empty($this->comments)){
			$xh = $this->getFullNewsXML();
			$this->comments = NewsComment::getComments($xh->getHrefValue(BRAFTON_COMMENTS));
		}
		return $this->comments;
	}

	/** @return String **/
	public function getExtract() {
		if(empty($this->extract)){
			$xh = $this->getFullNewsXML();
			$this->extract = $xh->getValue(BRAFTON_EXTRACT);
		}
		return $this->extract;
	}

	/** @return String **/
	public function getText() {
		if(empty($this->text)){
			$xh = $this->getFullNewsXML();
			$this->text = $xh->getValue(BRAFTON_TEXT);
			if(empty($this->text))throw new XMLNodeException("Element " . BRAFTON_TEXT . " for " . BRAFTON_NEWS_LIST_ITEM . " with id: " . $this->id . "<br />\n");
		}
		return $this->text;
	}

	/** @return String **/
	public function getByLine() {
		if(empty($this->byLine)){
			$xh = $this->getFullNewsXML();
			$this->byLine = $xh->getValue(BRAFTON_BY_LINE);
		}
		return $this->byLine;
	}

	/** @return String **/
	public function getTweetText() {
		if(empty($this->tweetText)){
			$xh = $this->getFullNewsXML();
			$this->tweetText = $xh->getValue(BRAFTON_TWEET_TEXT);
		}
		return $this->tweetText;
	}

	/** @return String **/
	public function getSource() {
		if(empty($this->source)){
			$xh = $this->getFullNewsXML();
			$this->source = $xh->getValue(BRAFTON_SOURCE);
		}
		return $this->source;
	}

	/** @return String **/
	public function getState() {
		if(empty($this->state)){
			$xh = $this->getFullNewsXML();
			$this->state = $xh->getValue(BRAFTON_STATE);
			if(empty($this->state))throw new XMLNodeException("Element " . BRAFTON_STATE . " for " . BRAFTON_NEWS_LIST_ITEM . " with id: " . $this->id . "<br />\n");
		}
		return $this->state;
	}

	/** @return String **/
	public function getClientQuote() {
		if(empty($this->clientQuote)){
			$xh = $this->getFullNewsXML();
			$this->clientQuote = $xh->getValue(BRAFTON_CLIENT_QUOTE);
		}
		return $this->clientQuote;
	}

	/** @return String **/
	public function getHtmlTitle() {
		if(empty($this->htmlTitle)){
			$xh = $this->getFullNewsXML();
			$this->htmlTitle = $xh->getValue(BRAFTON_HTML_TITLE);
		}
		return $this->htmlTitle;
	}

	/** @return String **/
	public function getHtmlMetaDescription() {
		if(empty($this->htmlMetaDescription)){
			$xh = $this->getFullNewsXML();
			$this->htmlMetaDescription = $xh->getValue(BRAFTON_HTML_META_DESCRIPTION);
		}
		return $this->htmlMetaDescription;
	}

	/** @return String **/
	public function getHtmlMetaKeywords() {
		if(empty($this->htmlMetaKeywords)){
			$xh = $this->getFullNewsXML();
			$this->htmlMetaKeywords = $xh->getValue(BRAFTON_HTML_META_KEYWORDS);
		}
		return $this->htmlMetaKeywords;
	}

	/** @return String **/
	public function getHtmlMetaLanguage() {
		if(empty($this->htmlMetaLanguage)){
			$xh = $this->getFullNewsXML();
			$this->htmlMetaLanguage = $xh->getValue(BRAFTON_HTML_META_LANGUAGE);
		}
		return $this->htmlMetaLanguage;
	}

	/** @return String **/
	public function getTags() {
		if(empty($this->tags)){
			$xh = $this->getFullNewsXML();
			$this->tags = $xh->getValue(BRAFTON_TAGS);
		}
		return $this->tags;
	}

	/** @return int **/
	public function getPriority() {
		if(empty($this->priority)){
			$xh = $this->getFullNewsXML();
			$this->priority = $xh->getValue(BRAFTON_PRIORITY);
		}
		return $this->priority;
	}

	/** @return String **/
	public function getFormat() {
		if(empty($this->format)){
			$xh = $this->getFullNewsXML();
			$this->htmlMetaLanguage = $xh->getAttributeValue(BRAFTON_TEXT, BRAFTON_FORMAT);
		}
		return $this->format;
	}
}
/**
 * Custom Exception DateParseException to be thrown if a date does not parse correctly
 * @package BraftonApi
 */
class DateParseException extends Exception{}
?>

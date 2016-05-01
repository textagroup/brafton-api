<?php

class BraftonService extends RestfulService {
    private $api;

    public function __construct() {
        $siteConfig = SiteConfig::current_site_config();
        $apiUrl = $siteConfig->BraftonApiUrl;
        $apiKey = $siteConfig->BraftonApiKey;
        if (!$apiUrl || !$apiKey) {
            return null;
        }

        $this->api = new \brafton\APIHandler($apiKey, $apiUrl);
    }

    public function getApi() {
        return $this->api;
    }

    /**
     * Check if a news item already exists in the Dataobject
     * and adds it if it does not exist
     */
    public function addNews($item) {
        // check if the item already exists via the ApiID or create one
        $news = BraftonNewsItem::get()
            ->filter('ApiID', $item->getId())
            ->first();
        if (!$news || !$news->exists()) {
            $news = new BraftonNewsItem();
            $news->ApiID = $item->getId();
            $news->CreatedDate = $item->getCreatedDate();
        } else if ($news->LastModifiedDate == $item->getLastModifiedDate()) {
            continue;
        }
        // update fields
        $news->PublishDate = $item->getPublishDate();
        $news->CreatedDate = $item->getCreatedDate();
        $news->LastModifiedDate = $item->getLastModifiedDate();
        $news->Headline = $item->getHeadline();
        $news->HtmlTitle = $item->getHtmlTitle();
        $news->HtmlMetaDescription = $item->getHtmlMetaDescription();
        $news->Content = $item->getText();
        $categoryList = array();
        foreach ($item->getCategories() as $category) {
            $categoryList[] = $category->getId();
            $newsCategory = BraftonNewsCategory::get()
                ->filter('CategoryID', $category->getId())
                ->first();
            if (!$newsCategory || !$newsCategory->exists()) {
                $newsCategory = new BraftonNewsCategory();
                $newsCategory->CategoryID = $category->getId();
                $newsCategory->Title = $category->getName();
                $newsCategory->write();
            }
            $news->Categories()->add($newsCategory);
        }
        // removed old categories no longer on the news item
        foreach ($news->Categories()->exclude('CategoryID', $categoryList) as $delete) {
            $news->Categories()->remove($delete);
        }
        $news->write();
    }
}

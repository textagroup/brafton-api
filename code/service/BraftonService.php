<?php

class BraftonService extends RestfulService {
    private $api;

    private $folder = 'brafton-images';

    public function __construct() {
        $siteConfig = SiteConfig::current_site_config();
        $apiUrl = $siteConfig->BraftonApiUrl;
        $apiKey = $siteConfig->BraftonApiKey;
        if (!$apiUrl || !$apiKey) {
            return null;
        }

        $this->api = new \brafton\APIHandler($apiKey, $apiUrl);
    }

    /**
     * Set the image folder
     * @param String Image folder name
     */
    public function setImageFolder($name = 'brafton-images') {
        $this->folder = $name;
    }

    /**
     * get the image folder
     * @return String Image folder name
     */
    public function getImageFolder() {
        return $this->folder;
    }

    /**
     * Check if a news item already exists in the Dataobject
     * and adds it if it does not exist
     * @return Object Brafton API
     */
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
        } else {
            $recordModifiedDate = strtotime($news->LastModifiedDate);
            $itemModifiedDate = strtotime($item->getLastModifiedDate());
            if ($recordModifiedDate == $itemModifiedDate) {
                return;
            }
        }
        // update fields
        $news->PublishDate = $item->getPublishDate();
        $news->CreatedDate = $item->getCreatedDate();
        $news->LastModifiedDate = $item->getLastModifiedDate();
        $news->Headline = $item->getHeadline();
        $news->Extract = $item->getExtract();
        $news->Title = $item->getHtmlTitle();
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

        // deal with any photos
        foreach ($item->getPhotos() as $photo) {
            // images
            $url = $photo->getLarge()->getUrl();
            $file = $this->createImageFile($photo, $url, $item);
            $imageObj = new BraftonNewsItemImage();
            $imageObj->ImageID = $file->ID;
            $thumb = $photo->getThumb();
            // TODO Fully test this
            // unable to fully test as test details do not have thumbnails
            if ($thumb) {
                $url = $photo->getThumb()->getUrl();
                // API returns the string NULL if there is no thumbnail
                if ($url != 'NULL') {
                    $file = $this->createImageFile($photo, $url, $item);
                    $imageObj->ThumbID = $file->ID;
                }
            }
            $imageObj->write();

            $news->Photos()->add($imageObj);
        }

        $news->write();
        $categoriesUpdated = false;
        // removed old categories no longer on the news item
        foreach ($news->Categories()->exclude('CategoryID', $categoryList) as $delete) {
            $news->Categories()->remove($delete);
            $categoriesUpdated = true;
        }
        if ($categoriesUpdated) {
            $news->write();
        }
    }

    /*
     * Create a image file from a Brafton photo instance and URL
     * @param Object Brafton Photo Instance
     * @param String URL of image
     * @param Object Brafton news instance
     * @return File File Object
     */
    public function createImageFile($photo, $url, $item) {
        $tmpImage = tmpfile();
        $fileContents = file_get_contents($url);
        fwrite($tmpImage,$fileContents);

        // check if this is a valid image
        $metaData = stream_get_meta_data($tmpImage);
        $imageSizeInfo = getimagesize($metaData['uri']);
        if (!$imageSizeInfo) {
            continue;
        }

        $imageFolder = Folder::find_or_make($this->folder . '/' . $item->getId());
        $name = basename($url);
        $fileSize = filesize($metaData['uri']);
        $tmpName = $metaData['uri'];
        $relativeImagePath = $imageFolder->getRelativePath() . $name;
        $imagePath = BASE_PATH . '/' . $relativeImagePath;

        fclose($tmpImage);

        if (file_exists($imagePath)) {
            $pathInfo = pathinfo($url);
            if (isset($pathInfo['extension'])) {
                $name = basename($tmpName) . '.' . $pathInfo['extension'];
                $relativeImagePath = $imageFolder->getRelativePath() . $name;
                $imagePath = BASE_PATH . '/' . $relativeImagePath;
            }
        }

        $image = fopen($imagePath, 'w');
        fwrite($image, $fileContents);
        fclose($image);

        $file = new File();
        $file->setParentID($imageFolder->ID);
        $file->setName($name);
        $file->Title = $photo->getAlt();
        $file->setFilename($relativeImagePath);
        $file->write();
        return $file;
    }
}

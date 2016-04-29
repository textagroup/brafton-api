 <?php

/**
 * A job for fetching blog items from the Brafton API
 *
 * Connects to Brafton API and fetches blog items
 *
 * @author kirk.mayo@solnet.co.nz
*/

class FetchBraftonBlogItemsJob extends BuildTask {
    protected $title = 'Brafton API Job';

    protected $description = 'Fetches potential blog items form the Brafton API';

    public function run($request) {
        $service = new BraftonService();
        $api = $service->getApi();
        if ($api) {
            $newsItems = $api->getNewsHTML();
            foreach ($newsItems as $item) {
                //TODO Move this into it's own method on BraftonService but I want to go home now
                // so I will do it next week it's Friday now and I want a beer
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
                //TODO remove relations that are no longer needed
                foreach ($item->getCategories() as $category) {
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
                $news->write();
            }
        } else { 
            echo '<p>Not Connected</p>';
        }   
    }   
}  

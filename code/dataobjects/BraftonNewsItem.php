<?php

/**
* @author Kirk Mayo kirk.mayo@solnet.co.nz
* Brafton News Item dataobject
*/

class BraftonNewsItem extends DataObject
{
    private static $singular_name = 'News Item';
    private static $plural_name = 'News Items';

    private static $db = array(
        'ApiID' => 'Text',
        'PublishDate' => 'Datetime',
        'CreatedDate' => 'Datetime',
        'LastModifiedDate' => 'Datetime',
        'Headline' => 'Text',
        'Categories' => 'Text',
        'HtmlTitle' => 'Text',
        'HtmlMetaDescription' => 'Text',
        'Content' => 'HTMLText'
    );

    private static $has_many = array(
        'Photos' => 'BraftonNewsItemImage'
    );

    private static $many_many = array(
        'Categories' => 'BraftonNewsCategory'
    );
}

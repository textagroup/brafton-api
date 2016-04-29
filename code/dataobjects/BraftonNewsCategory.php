<?php

/**
* @author Kirk Mayo kirk.mayo@solnet.co.nz
* Brafton News Category dataobject
*/

class BraftonNewsCategory extends DataObject
{
    private static $singular_name = 'News Category';
    private static $plural_name = 'News Categories';

    private static $db = array(
        'CategoryID' => 'Text',
        'Title' => 'Varchar'
    );
}

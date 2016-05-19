# Brafton API

## Introduction

This module is used for the Brafton/Castleford API

Copyright (C) 2010-2014 kirk.mayo@solnet.co.nz

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.


## Maintainer Contact

    * Kirk Mayo kirk.mayo (at) solnet (dot) co.nz

## Requirements

    * SilverStripe 3.0 +

## Features

* Connects to Brafton/Castleford API

## Composer Installation

  composer require textagroup/brafton-api
  
## Installation Manual

 1. Download the module form GitHub (Composer support to be added)
 2. Extract the file (if you are on windows try 7-zip for extracting tar.gz files
 3. Make sure the folder after being extracted is named 'brafton-api'
 4. Place this directory in your sites root directory. This is the one with framework and cms in it.
 5. Run in your browser - `/dev/build` to rebuild the database.

## Usage ##

To use the module you will need to specify the api key and api URL in the Brafton
tab of settings.

The module contains a task to connect to the API and retrieve the content and images from the
Brafton API.

The images are stored in assets as a normal SilverStripe Image and the items and categories
are stored in the DataObjects BraftonNewsItem and BraftonNewsCategory.

This module acts as a wrapper for the [http://www.brafton.com/support/php-sample/](Brafton API library).

A good example of how this works is the job to fetch the blog items [code/jobs/FetchBraftonBlogItemsJob.php](FetchBraftonBlogItemsJob). 
This calls the Braftonservice class and the constructor handles connecting and creating a API object which can then be retrieved by the method getApi

```
        $service = new BraftonService();
        $api = $service->getApi();
        if ($api) {
            $newsItems = $api->getNewsHTML();
            foreach ($newsItems as $item) {
                $service->addNews($item);
            }
        }
```

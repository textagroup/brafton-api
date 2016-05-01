<?php

/**
 * Adds a Brafton settings tab
 */
class BraftonSiteConfigExtension extends DataExtension {
    private static $db = array(
        'BraftonApiUrl' => 'Varchar',
        'BraftonApiKey' => 'Varchar'
    );

    public function updateCMSFields(FieldList $fields) {
        $fields->addfieldToTab('Root.Brafton',
            TextField::create(
                'BraftonApiUrl',
                _t('BraftonApi.BRAFTONAPIURL', 'Brafton Api Url')
            )
        );
        $fields->addfieldToTab('Root.Brafton',
            TextField::create(
                'BraftonApiKey',
                _t('BraftonApi.BRAFTONAPIKEY', 'Brafton Api Key')
            )
        );
    }

	public function requireDefaultRecords() {
		parent::requireDefaultRecords();

        $siteConfig = SiteConfig::current_site_config();
        if ($siteConfig->BraftonApiUrl == null) {
            $siteConfig->BraftonApiUrl
                = _t('BraftonApi.DEFAULTAPIURL', 'http://api.brafton.com/');
            $siteConfig->write(); 
        }
    }
}

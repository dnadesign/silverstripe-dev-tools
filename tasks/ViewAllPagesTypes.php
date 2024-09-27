<?php

namespace DNADesign\DevTools;

use SilverStripe\Control\Director;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;
use SilverStripe\Versioned\Versioned;

/**
 * Undocumented class
 */
class ViewAllPagesTypes extends BuildTask
{
    protected $title = 'View All Page Types';

    protected $description = 'Provides a list of the Page Types on the site with links to the front end and CMS.';

    private static $segment = 'ViewAllPagesTypes';

    public function __construct()
    {
        if (!Director::is_cli()) {
            $path = ModuleResourceLoader::singleton()->resolveURL('silverstripe/framework:client/styles/task-runner.css');
            $cssFile = sprintf('<link rel="stylesheet" type="text/css" href="%s" />', $path);
            echo $cssFile;
        }
    }

    public function run($request)
    {
        $oldReadingMode = Versioned::get_reading_mode();
        Versioned::set_reading_mode('live');

        $pageTypes = DB::query('SELECT DISTINCT ClassName FROM SiteTree;')->column();
        echo '<div class="task__panel">' .
                '<div class="task__list">';
        foreach ($pageTypes as $className) {
            $randomPage = $className::get()->shuffle()->first();
            echo    '<div class="task__item task__item--universal">' .
                        '<div>' .
                            '<h3 class="task__title">Page Title:' . $randomPage->Title . '</h3>' .
                            '<div class="task__description">Page Class:' . $className . '</div>' .
                        '</div>'.
                        '<div>' .
                            '<a href="' . $randomPage->CMSEditLink() . '" class="task__button" target="_blank">CMS Edit Link</a>' .
                            '<a href="' . $randomPage->AbsoluteLink() . '" class="task__button" target="_blank">Dev Site Link</a>' .
                        '</div>'.
                    '</div>';
        }
        echo    '</div>' .
            '</div>';

        Versioned::set_reading_mode($oldReadingMode);
    }
}

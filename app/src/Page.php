<?php

namespace {

    use DNADesign\Populate\Populate;
    use SilverStripe\Assets\File;
    use SilverStripe\Assets\Folder;
    use SilverStripe\Assets\Image;
    use SilverStripe\CMS\Model\SiteTree;
    use SilverStripe\Core\Config\Config;
    use SilverStripe\ORM\DataObject;
    use SilverStripe\ORM\Queries\SQLUpdate;

    class Page extends SiteTree
    {
        private static $db = [];

        private static $has_one = [];

        /**
         * @internal
         */
        private static $hasSetDefaults = false;

        public function requireDefaultRecords()
        {
            // Populate is silly and thinks all files are images... so we have to tell it pdf is a file temporarily.
            $originalConfig = File::config()->get('app_categories');
            Config::modify()->merge(File::class, 'app_categories', ['image/supported' => ['pdf']]);
            Populate::requireRecords();
            Config::modify()->set(File::class, 'app_categories', $originalConfig);
            // Only fix the files once per dev/build
            if (!self::$hasSetDefaults) {
                foreach (File::get() as $file) {
                    /** @var File $file */
                    if ($file instanceof Folder) {
                        continue;
                    }
                    // Make images be images, and files be files
                    $category = File::get_app_category($file->getExtension());
                    $update = SQLUpdate::create(DataObject::getSchema()->baseDataTable(File::class));
                    $update->setWhere(['ID = ?' => $file->ID]);
                    if (in_array($category, ['image', 'image/supported'])) {
                        $update->setAssignments(['ClassName' => Image::class]);
                    } else {
                        $update->setAssignments(['ClassName' => File::class]);
                    }
                    $update->execute();
                }
            }
            self::$hasSetDefaults = true;
        }
    }
}

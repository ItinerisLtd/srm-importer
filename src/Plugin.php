<?php
declare(strict_types=1);

namespace Itineris\SRMImporter;

class Plugin
{
    public const PREFIX = 'srm_safe_importer';

    public function run(): void
    {
        add_action('admin_init', [ImporterPage::class, 'registerSettings']);
        add_action('admin_menu', [ImporterPage::class, 'addManagementPage']);
        add_filter('upload_mimes', [UploadMimes::class, 'allowCSV']);
        // Do not save importer page options.
        add_filter('pre_update_option_' . ImporterPage::CSV_FILE_OPTION_ID, '__return_false', 1000);
        add_action('pre_update_option_' . ImporterPage::CSV_FILE_OPTION_ID, [ImporterPage::class, 'handleFormSubmit']);
    }
}

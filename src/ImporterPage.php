<?php
declare(strict_types=1);

namespace Itineris\SRMImporter;

use AdamWathan\Form\FormBuilder;
use TypistTech\WPBetterSettings\Field;
use TypistTech\WPBetterSettings\Registrar;
use TypistTech\WPBetterSettings\Section;

class ImporterPage
{
    private const SLUG = Plugin::PREFIX . 'csv_importer';
    public const CSV_FILE_OPTION_ID = Plugin::PREFIX . 'csv_file';

    public static function registerSettings(): void
    {
        $section = new Section(
            'csv_importer',
            __('Import redirection rules via CSV file', 'srm-importer')
        );

        // Because wp-better-settings does not support file field yet.
        $formBuilder = new FormBuilder();

        $section->add(
            new Field(
                self::CSV_FILE_OPTION_ID,
                __('CSV File', 'srm-importer'),
                $formBuilder->file(self::CSV_FILE_OPTION_ID)
                            ->id(self::CSV_FILE_OPTION_ID),
                []
            )
        );
        $registrar = new Registrar(self::SLUG);
        $registrar->add($section);
        $registrar->run();
    }

    public static function addManagementPage(): void
    {
        add_management_page(
            __('SRM Importer', 'srm-importer'),
            __('SRM Importer', 'srm-importer'),
            'manage_options',
            self::SLUG,
            function () {
                echo '<div class="wrap">';
                settings_errors();
                echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';
                echo '<form action="options.php" method="post" enctype="multipart/form-data">';
                settings_fields(self::SLUG);
                do_settings_sections(self::SLUG);
                submit_button();
                echo '</form>';
                echo '</div>';
            }
        );
    }

    public static function handleFormSubmit(): void
    {
        [
            'error' => $message,
            'success' => $uploadSuccess,
            'path' => $csvFilePath,
        ] = self::handleUpload();

        if ($uploadSuccess) {
            [
                'created' => $created,
                'skipped' => $skipped,
            ] = srm_import_file($csvFilePath, [
                'source' => 'source',
                'target' => 'target',
                'regex' => 'regex',
                'code' => 'code',
                'order' => 'order',
            ]);

            $message = "{$created} created and {$skipped} skipped";
        }

        add_settings_error(
            self::SLUG,
            esc_attr('settings_updated'),
            $message,
            $uploadSuccess ? 'updated' : 'error'
        );
    }

    private static function handleUpload(): array
    {
        if (empty($_FILES)) { // Input var okay.
            return [
                'success' => false,
                'error' => esc_html__('Failed to accept CSV file. $_FILES is empty.', 'srm-importer'),
                'path' => null,
            ];
        }

        $files = wp_unslash($_FILES); // Input var okay.
        $file = $files[self::CSV_FILE_OPTION_ID] ?? null;
        if (empty($file)) {
            return [
                'success' => false,
                'error' => esc_html__(
                    'Failed to accept CSV file. $files[self::CSV_FILE_OPTION_ID] is empty.',
                    'srm-importer'
                ),
                'path' => null,
            ];
        }

        $moveFile = wp_handle_upload($file, ['test_form' => false]);
        if (! is_array($moveFile) || isset($moveFile['error'])) {
            return [
                'success' => false,
                'error' => $moveFile['error'],
                'path' => null,
            ];
        }

        return [
            'success' => true,
            'error' => null,
            'path' => $moveFile['file'],
        ];
    }
}

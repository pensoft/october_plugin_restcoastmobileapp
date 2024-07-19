<?php
namespace Pensoft\RestcoastMobileApp\Controllers;

use Backend\Behaviors\FormController;
use Backend\Classes\Controller;
use BackendMenu;

use Illuminate\Support\Facades\App;
use Pensoft\RestcoastMobileApp\Models\AppSettings as SettingsModel;

/**
 * Settings Controller Backend Controller
 */
class AppSettings extends Controller
{
    public $implement = [FormController::class];

    public $settingsItemCode = 'pensoft_restcoast_settings';
    public $formConfig = 'config_form.yaml';

    private $model;

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = SettingsModel::instance();

        BackendMenu::setContext(
            'Pensoft.RestcoastMobileApp',
            'restcoast',
            'app_settings'
        );
    }

    public function index()
    {
        $this->pageTitle = 'App Settings';
        $this->initForm($this->model);
    }

    public function onSave()
    {
        $uploader      = App::make("JsonUploader");
        $jsonGenerator = App::make("JsonGenerator");
        $translator    = App::make("TranslationService");

        $entries = $this->model::all();
        $entry   = $entries->first();

        if ($entry == null) {
            $this->create_onSave(FormController::CONTEXT_CREATE);
        } else {
            $this->update_onSave($entry->id, FormController::CONTEXT_UPDATE);
        }

        $translations = $translator->getOneWithTranslations($entry);
        foreach ($translations as $locale => $translation) {
            $uploader->uploadJson($jsonGenerator->generateJson($translation), "l/".$locale."/home.json");
        }

    }
}

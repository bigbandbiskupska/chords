<?php
/**
 * Created by PhpStorm.
 * User: ktulinger
 * Date: 21/04/2018
 * Time: 10:27
 */

namespace App\FrontModule\Presenters;


use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Tulinkry\Application\UI\Presenter;
use Tulinkry\Services\ParameterService;

class BasePresenter extends Presenter
{

    /**
     * @var ParameterService
     * @inject
     */
    public $parameters;

    public function startup()
    {
        parent::startup();
        $timestamp = $this->parameters->params['appDir'] . DIRECTORY_SEPARATOR . 'timestamp';
        if (file_exists($timestamp)) {
            $this->template->lastUpdated = DateTime::from(@filemtime($timestamp));
        }
    }

    public static function update()
    {
        $timestamp = APP_DIR . DIRECTORY_SEPARATOR . 'timestamp';
        if (file_exists($timestamp)) {
            FileSystem::delete($timestamp);
        }
        FileSystem::write($timestamp, "");
    }

    public static function distance($base, $note)
    {
        static $notes = [
            ['his', 'c', 'deses'],
            ['cis', 'des', 'hisis'],
            ['cisis', 'd', 'eses'],
            ['dis', 'es', 'feses'],
            ['disis', 'e', 'fes'],
            ['geses', 'eis', 'f'],
            ['eisis', 'fis', 'ges'],
            ['fisis', 'g', 'asas'],
            ['gis', 'as'],
            ['gisis', 'a', 'heses'],
            ['b', 'ais', 'ceses'],
            ['ces', 'h', 'aisis'],
        ];

        foreach ($notes as $i => $enharmonic_notes) {
            if (in_array($base, $enharmonic_notes)) {
                break;
            }
        }

        $distance = 0;
        while (!in_array($note, $notes[$i])) {
            $distance += 1;
            $i = ($i + 1) % count($notes);
        }
        return $distance;
    }

}

class X
{
    public $notes = array();

    public function __construct()
    {
        $this->notes = func_get_args();
    }
}
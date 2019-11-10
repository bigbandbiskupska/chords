<?php
/**
 * Created by PhpStorm.
 * User: ktulinger
 * Date: 21/04/2018
 * Time: 10:27
 */

namespace App\FrontModule\Presenters;


use Nette\Http\Session;
use Nette\Http\SessionSection;
use Tulinkry\Application\UI\Form;

class TestPresenter extends BasePresenter
{

    /** @var SessionSection */
    private $session;

    /**
     * ChordsPresenter constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session->getSection('test');
    }

    protected function createComponentTestForm($name)
    {
        $form = new Form();

        $settings = $form->addContainer('settings');
        $settings->addCheckboxList('family', 'Tónorod', [
            'minor' => 'Molové',
            'major' => 'Durové',
        ])->setDefaultValue([
            'major',
            'minor',
        ]);

        $settings->addCheckboxList('type', 'Typ akordů', [
            'kvintakord' => 'Kvintakordy',
            'sextakord' => 'Sextakordy',
            'kvartsextakord' => 'Kvartsextakordy',
        ])->setDefaultValue([
            'kvintakord',
        ]);


        $settings->addCheckboxList('notes', 'Použité základní noty', [
            'basic_notes' => 'Základní noty',
            'first_augmented_notes' => 'Jeden #/jedno b',
            'second_augmented_notes' => 'Dvě #/dvě b'
        ])->setDefaultValue([
            'basic_notes',
            'first_augmented_notes',
        ]);

        $form->addText('questions', 'Počet otázek')
            ->addRule(Form::INTEGER, 'Počet otázek musí být číslo')
            ->addRule(Form::RANGE, 'Počet otázek musí být v intervalu od %d do %d', [1, 10000])
            ->setRequired();

        $form->onSuccess[] = function ($form) {
            $values = $form->values;
            $this->session->test = (object)array_merge(
                (array)$values['settings'],
                (array)$values,
                [
                    'answered' => 0,
                    'successful' => 0,
                    'failed' => 0,
                ]
            );
            $this->session->last_test_settings = (array)$values;
            $this->redirect('Chords:default', ['question' => 1]);
        };
        $form->addSubmit('submit', 'Začít test')
            ->setAttribute('class', 'btn btn-primary');

        return $this[$name] = $form;
    }


    public function actionDefault()
    {
        unset($this->session->test);
        unset($this->session->questions);
        $this['testForm']->setDefaults(
            $this->session->last_test_settings ?: []
        );
    }

    public function actionAnswer()
    {
        $this->template->test = $this->session->test;
        $this->template->questions = $this->session->questions;
    }
}
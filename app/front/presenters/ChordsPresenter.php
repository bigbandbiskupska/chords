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

class ChordsPresenter extends BasePresenter
{

    public $notes = array(
        'c', 'd', 'e', 'f', 'g', 'a', 'h'
    );

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

    public function interval($base, $interval, $variant)
    {
        $base_index = array_search($base, $this->notes);
        $next_note = $this->notes [($base_index + $interval - 1) % count($this->notes)];
        switch ($variant) {
            case 'v':
                return $next_note;
            case 'm':
                return $next_note . 'es';
            case 'zm':
                return $next_note . 'es';
            case 'zmzm':
                return $next_note . 'es';
            case 'zv':
                return $next_note . 'is';
            case 'zvzv':
                return $next_note . 'is';
            default:
                return $next_note;
        }
    }

    public $major_chords = array(
        'his' => ['his', 'disis', 'fisis'],
        'c' => ['c', 'e', 'g'],
        'deses' => ['deses', 'fes', 'asas'],

        'cis' => ['cis', 'eis', 'gis'],
        'des' => ['des', 'f', 'as'],

        'cisis' => ['cisis', 'eisis', 'gisis'],
        'd' => ['d', 'fis', 'a'],
        'eses' => ['eses', 'ges', 'heses'],

        'dis' => ['dis', 'fisis', 'ais'],
        'es' => ['es', 'g', 'b'],
        'feses' => ['feses', 'asas', 'ceses'],

        'e' => ['e', 'gis', 'h'],
        'fes' => ['fes', 'as', 'ces'],

        'eis' => ['eis', 'gisis', 'his'],
        'f' => ['f', 'a', 'c'],
        'geses' => ['geses', 'heses', 'deses'],

        'fis' => ['fis', 'ais', 'cis'],
        'ges' => ['ges', 'b', 'des'],

        'fisis' => ['fisis', 'aisis', 'cisis'],
        'g' => ['g', 'h', 'd'],
        'asas' => ['asas', 'ces', 'eses'],

        'gis' => ['gis', 'his', 'dis'],
        'as' => ['as', 'c', 'es'],

        'gisis' => ['gisis', 'hisis', 'disis'],
        'a' => ['a', 'cis', 'e'],
        'heses' => ['heses', 'des', 'fes'],

        'ais' => ['ais', 'cisis', 'eis'],
        'b' => ['b', 'd', 'f'],
        'ceses' => ['ceses', 'eses', 'geses'],

        'h' => ['h', 'dis', 'fis'],
        'ces' => ['ces', 'es', 'ges'],
    );

    public $minor_chords = array(
        'his' => ['his', 'dis', 'fisis'],
        'c' => ['c', 'es', 'g'],
        'deses' => ['deses', 'feses', 'asas'],

        'cis' => ['cis', 'e', 'gis'],
        'des' => ['des', 'fes', 'as'],

        'cisis' => ['cisis', 'eis', 'gisis'],
        'd' => ['d', 'f', 'a'],
        'eses' => ['eses', 'geses', 'heses'],

        'dis' => ['dis', 'fis', 'ais'],
        'es' => ['es', 'ges', 'b'],

        'disis' => ['disis', 'fisis', 'aisis'],
        'e' => ['e', 'g', 'h'],
        'fes' => ['fes', 'asas', 'ces'],

        'eis' => ['eis', 'gis', 'his'],
        'f' => ['f', 'as', 'c'],

        'eisis' => ['eisis', 'gisis', 'hisis'],
        'fis' => ['fis', 'a', 'cis'],
        'ges' => ['ges', 'heses', 'des'],

        'fisis' => ['fisis', 'ais', 'cisis'],
        'g' => ['g', 'b', 'd'],
        'asas' => ['asas', 'ceses', 'eses'],

        'gis' => ['gis', 'h', 'dis'],
        'as' => ['as', 'ces', 'es'],

        'gisis' => ['gisis', 'his', 'disis'],
        'a' => ['a', 'c', 'e'],
        'heses' => ['heses', 'deses', 'fes'],

        'ais' => ['ais', 'cis', 'eis'],
        'b' => ['b', 'des', 'f'],

        'aisis' => ['aisis', 'cisis', 'eisis'],
        'h' => ['h', 'd', 'fis'],
        'ces' => ['ces', 'eses', 'ges'],
    );

    protected function createComponentChordQuestionForm($name)
    {
        $form = new Form();
        $form->addText('answer')
            ->setRequired('Musíte zadat odpověď')
            ->setHtmlAttribute('placeholder', 'např. c e g')
            ->setHtmlAttribute("autofocus", true);
        $form->onSuccess[] = function (Form $form) {
            $values = $form->values;

            $chords = $this->major_chords;
            switch ($values['family']) {
                case 'minor':
                    $chords = $this->minor_chords;
                    break;
                case 'major':
                    $chords = $this->major_chords;
                    break;
            }

            switch ($values['type']) {
                case 'kvartsextakord':
                    $chords = array_map(function ($chord) {
                        array_push($chord, array_shift($chord));
                        array_push($chord, array_shift($chord));
                        return $chord;
                    }, array_values($chords));
                    break;
                case 'kvintakord':
                    break;
                case 'sextakord':
                    $chords = array_map(function ($chord) {
                        array_push($chord, array_shift($chord));
                        return $chord;
                    }, array_values($chords));
                    break;
            }

            $chords = array_filter($chords, function ($chord) use ($values) {
                return $chord[0] === $values['note'];
            });

            $answered_chord = array_map('strtolower', array_map('trim', preg_split('/,|\s/', $values['answer'])));

            foreach ($chords as $chord) {
                if (count($chord) !== count($answered_chord)) {
                    $form->addError(sprintf('Tento akord má obsahovat právě %d tóny', count($chord)));
                    continue;
                }
                $diff = array_diff_assoc($answered_chord, $chord);
                if (count($diff) > 0) {
                    $form->addError('Špatná odpověď');
                    foreach ($diff as $i => $wrongNote) {
                        $form->addError(sprintf('%d. tón není %s', $i + 1, $wrongNote));
                    }
                }
                break;
            }

            $this->session->questions[$values['question']] = (object)[
                'note' => $values['note'],
                'type' => $values['type'],
                'family' => $values['family'],
                'answer' => $answered_chord,
                'correct_answer' => $chord,
                'diff' => isset($diff) ? $diff : [],
                'is_correct' => !$form->hasErrors()
            ];

            $this->session->test->answered = count($this->session->questions);
            $this->session->test->successful = count(array_filter($this->session->questions, function ($q) {
                return $q->is_correct;
            }));
            $this->session->test->failed = $this->session->test->answered - $this->session->test->successful;

            if ($form->hasErrors()) {
                $this->redirect('Chords:answer', ['question' => $values['question']]);
            } else if ($values['question'] >= $this->session->test->questions) {
                // correct answer and last question
                $this->redirect('Test:answer');
            } else {
                // correct answer
                $this->flashMessage('Správná odpověď', 'success');
                $this->redirect('Chords:default', ['question' => $values['question'] + 1]);
            }
        };

        $form->addHidden('family');
        $form->addHidden('type');
        $form->addHidden('note');
        $form->addHidden('question');
        $form->addSubmit('submit', 'Odpovědět')
            ->setAttribute('class', 'btn btn-primary');


        return $this[$name] = $form;
    }

    public function actionAnswer($question)
    {
        if (!isset($this->session->questions[$question])) {
            $this->flashMessage('Otázka ještě nebyla zodpovězená!', 'danger');
            $this->redirect('Chords:default', ['question' => $question]);
            return;
        }

        $this->template->question = $this->session->questions[$question];
        $this->template->current_question = (int)$question;
        $this->template->next_question = (int)$question + 1;
        $this->template->test = $this->session->test;
        $this->template->question_number = (int)$question;
    }

    public function generateChordType($types = ['kvintakord', 'sextakord', 'kvartsextarkord'])
    {
        return $types[array_rand($types)];
    }

    public function generateChordFamily($families = ['major', 'minor'])
    {
        return $families[array_rand($families)];
    }

    public function generateBaseNote($notes)
    {
        return $notes[array_rand($notes)];
    }

    public function generateQuestion(
        $notes,
        $families = ['major', 'minor'],
        $types = ['kvintakord', 'sextakord', 'kvartsextakord']
    )
    {
        return array(
            $this->generateBaseNote($notes),
            $this->generateChordFamily($families),
            $this->generateChordType($types)
        );
    }

    protected function onlyBasic($note)
    {
        return strlen($note) === 1;
    }

    protected function onlyFirstExtension($note)
    {
        return strlen($note) <= 3 && strlen($note) > 1;
    }

    protected function onlySecondExtension($note)
    {
        return strlen($note) > 3;
    }

    public function prepareNotes()
    {
        $notes = [];
        if (in_array('second_augmented_notes', $this->session->test->notes)) {
            $notes = array_merge($notes, array_filter(array_unique(array_keys($this->major_chords)), [$this, 'onlySecondExtension']));
        }
        if (in_array('first_augmented_notes', $this->session->test->notes)) {
            $notes = array_merge($notes, array_filter(array_unique(array_keys($this->major_chords)), [$this, 'onlyFirstExtension']));
        }
        if (in_array('basic_notes', $this->session->test->notes)) {
            $notes = array_merge($notes, array_filter(array_unique(array_keys($this->major_chords)), [$this, 'onlyBasic']));
        }

        return array_unique($notes);
    }

    public function actionDefault($question)
    {
        if (!isset($this->session->test)) {
            $this->flashMessage('Nejste v průběhu testu!', 'danger');
            $this->redirect('Test:default');
            return;
        }

        if (!isset($this->session->questions)) {
            $this->session->questions = [];
        }

        if (!isset($this->session->questions[$question])) {

            list($note, $family, $type) = $this->generateQuestion(
                $this->prepareNotes(),
                $this->session->test->family,
                $this->session->test->type
            );
            $this->session->questions[$question] = (object)array(
                'note' => $note,
                'family' => $family,
                'type' => $type,
            );
        } else {
            $note = $this->session->questions[$question]->note;
            $family = $this->session->questions[$question]->family;
            $type = $this->session->questions[$question]->type;
        }

        $this['chordQuestionForm']->setDefaults([
            'family' => $family,
            'type' => $type,
            'note' => $note,
            'question' => $question
        ]);

        $this->template->question = $this->session->questions[$question];
        $this->template->question_number = (int)$question;
        $this->template->question_n = $this->session->test->questions;
    }


}
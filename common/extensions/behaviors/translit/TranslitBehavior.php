<?php

/**
 * TranslitBehavior class file.
 *
 * @author
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
class TranslitBehavior extends CActiveRecordBehavior
{

    /** @var string */
    public $sourceAttribute = 'title';

    /** @var string */
    public $aliasAttribute = 'alias';
    public $scenarios = array('insert', 'update');
    protected $cleanList = array(
        '`&([a-z]+)(acute|grave|circ|cedil|tilde|uml|lig|ring|caron|slash);`i' => '\1',
        '`&(amp;)?[^;]+;`i' => '-',
        '`[^a-z0-9]`i' => '-',
        '`[-]+`' => '-',
    );
    protected $replaceList = array(
        'э' => 'je',
        'ё' => 'jo',
        'я' => 'ya',
        'ю' => 'yu',
        'ы' => 'y',
        'ж' => 'zh',
        'й' => 'y',
        'щ' => 'shch',
        'ч' => 'ch',
        'ш' => 'sh',
        'э' => 'ea',
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'з' => 'z',
        'и' => 'i',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'h',
        'ц' => 'c',
        'э' => 'e',
        'ь' => '',
        'ъ' => '',
        'й' => 'y',
        'Э' => 'JE',
        'Ё' => 'JO',
        'Я' => 'YA',
        'Ю' => 'YU',
        'Ы' => 'Y',
        'Ж' => 'ZH',
        'Й' => 'Y',
        'Щ' => 'SHCH',
        'Ч' => 'CH',
        'Ш' => 'SH',
        'Э' => 'E',
        'А' => 'A',
        'Б' => 'B',
        'В' => 'V',
        'Г' => 'G',
        'Д' => 'D',
        'Е' => 'E',
        'З' => 'Z',
        'И' => 'I',
        'К' => 'K',
        'Л' => 'L',
        'М' => 'M',
        'Н' => 'N',
        'О' => 'O',
        'П' => 'P',
        'Р' => 'R',
        'С' => 'S',
        'Т' => 'T',
        'У' => 'U',
        'Ф' => 'F',
        'Х' => 'H',
        'Ц' => 'C',
        'Э' => 'E',
        'Ь' => '',
        'Ъ' => '',
        'Й' => 'Y',
    );

    public function attach($owner)
    {

        parent::attach($owner);
        if (in_array($owner->getScenario(), $this->scenarios)) {

            $matchValidator = CValidator::createValidator(
                'match',
                $owner,
                $this->aliasAttribute,
                array(
                    'pattern' => '/([a-zA-Z]+)|([a-zA-Z]+)/',
                    'message' => 'поле должно содержать только буквы латинского алфавита'
                )
            );
//            $uniqueValidator = CValidator::createValidator(
//                            'unique', $owner, $this->aliasAttribute, array(
//                        'allowEmpty' => true
//                            )
//            );

            $owner->validatorList->add($matchValidator);
            //$owner->validatorList->add($uniqueValidator);
        }
    }

    public function beforeValidate($event)
    {
        $owner = $this->getOwner();
        if (trim($owner->{$this->aliasAttribute}) == '') {
            $owner->{$this->aliasAttribute} = $this->translit($owner->{$this->sourceAttribute});
        }
    }

    public function beforeSave($event)
    {
        $owner = $this->getOwner();
        if (trim($owner->{$this->aliasAttribute}) == '') {
            $owner->{$this->aliasAttribute} = $this->translit($owner->{$this->sourceAttribute});
        }
    }



    public function translit($source)
    {
        $source = str_replace(array_keys($this->replaceList), array_values($this->replaceList), $source);
        $source = htmlentities($source, ENT_COMPAT, 'UTF-8');
        $source = preg_replace(array_keys($this->cleanList), array_values($this->cleanList), $source);
        $source = strtolower(trim($source, '-'));
        return $source;
    }

}

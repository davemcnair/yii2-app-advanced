<?php
namespace common\models\base;

abstract class BaseModel extends \yii\db\ActiveRecord
{
    public $min;
    public $max;

    public function parentKey(){
        foreach ($this->tableSchema->columns as $attr=>$obj){
            if ($obj->comment=='parent'){
                return $attr;
            }
        }
        return '';
    }

    public static function relationFromId($keyAttr){
        $idLength=  \yii\helpers\StringHelper::endsWith($keyAttr,'_id')
            ?3:2;
        return substr($keyAttr,0,-$idLength);
    }

    public function optsParent(){
        $keyAttr=$this->parentKey();
        if (!$keyAttr){ return [''=>'Select']; };
        $rel=self::relationFromId($keyAttr);
        $parent=$this->$rel;
        return $parent?[$parent->id=>$parent]:[];
    }

    public function optsMoscow(){
        $opts=['M','S','C','W'];
        return array_combine($opts, $opts);
    }

    public function getCascadeCount(){
        return '[unspecified]';
    }
}

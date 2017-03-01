<?php

namespace common\components;

use Yii;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;

class EkoHelper
{

    public static function powered()
    {
        return 'Powered by <a href="https://www.ekouk.com/" rel="external">Eko UK</a>';
    }

/**
 * dedupe might come in handy sometime
 *
        $sql='CREATE TABLE location_telephone_deduped like location_telephone;
INSERT location_telephone_deduped SELECT * FROM location_telephone GROUP BY location_id, telephone_id;
RENAME TABLE location_telephone TO location_telephone_with_dupes;
RENAME TABLE location_telephone_deduped TO location_telephone;
DROP TABLE location_telephone_with_dupes;';
        \Yii::$app->db->createCommand($sql)->execute();
*/
    public static function dupeSql($table, $distinct, $pkColumnsCsv='id'){
        $tablePks="";
        $dupePks="";
        $groupPks="";
        $onPks="";
        $countPk="";
        $pks=explode(',',$pkColumnsCsv);
        foreach ($pks as $ix=>$pk){
            $tablePks.="{$table}.{$pk},";
            $dupePks.="{$pk},";
            $groupPks.="{$pk},";
            $onPks.=" AND {$table}.{$pk} = dupe.{$pk}";
            if (!$ix){ $countPk=$pk;}
        }
        $tablePks=rtrim($tablePks,',');
        $dupePks=rtrim($dupePks,',');
        $groupPks=rtrim($groupPks,',');
        $onPks=substr($onPks,5);
        $distinct=$distinct?'DISTINCT':'';
        $sql="
            SELECT {$distinct} {$tablePks}
            FROM {$table}
            INNER JOIN (
                SELECT {$dupePks}
                FROM {$table}
                GROUP BY {$groupPks}
                HAVING COUNT({$countPk}) > 1
            ) dupe
            ON {$onPks}
        ";
        return $sql;
    }

    public static function nonDistinctDupesCount($db, $table, $dupeKeys){
        $cmd=$db->createCommand(self::dupeSql($table, false, $dupeKeys));
        return count($cmd->queryAll());
    }

    public static function distinctDupes($db, $table, $dupeKeys){
        $cmd=$db->createCommand(self::dupeSql($table, true, $dupeKeys));
        $rows=$cmd->queryAll();
        return $rows;
    }

    public static function orphanSql($orphanKey, $childTable, $parentTable, $treatNullsAsOrphans=false){
        $nullClause=$treatNullsAsOrphans?"":"AND child.{$orphanKey} is not null";
        $sql="
            SELECT distinct {$orphanKey} FROM `{$childTable}`
            WHERE {$orphanKey} in (SELECT distinct {$orphanKey}
            FROM `{$childTable}` child
            LEFT JOIN  `{$parentTable}` parent ON parent.id = child.{$orphanKey}
            WHERE parent.id IS NULL
            {$nullClause}
            )
        ";
        return $sql;
    }

    public static function orphanCheck($db, $orphanKey, $childTable, $parentTable, $treatNullsAsOrphans){
        $cmd=$db->createCommand(self::orphanSql($orphanKey, $childTable, $parentTable, $treatNullsAsOrphans));
        $keyIds=$cmd->queryColumn();
        return $keyIds;
    }

    public static function dbDsnName($db){
        preg_match('/dbname=([^;]*)/', $db->dsn, $databaseMatches);
        $databaseName = $databaseMatches[1];
        return " {$databaseName} ";
    }

    public static function dbDsnLogin($db){
        preg_match('/host=([^;]*)/', $db->dsn, $hostMatches);
        $hostName = $hostMatches[1];
        preg_match('/port=([^;]*)/', $db->dsn, $portMatches);
        if (isset($portMatches[1])) {
            $port = $portMatches[1];
        } else {
            $port = "3306";
        }
        return " -h{$hostName} -P{$port} -u{$db->username} -p{$db->password} ";
    }

    public static function dbDsnParams($db){
        return self::dbDsnLogin($db).self::dbDsnName($db);
    }

    public static function str_replace_first($from, $to, $subject)
    {
        $from = '/'.preg_quote($from, '/').'/';
        return preg_replace($from, $to, $subject, 1);
    }

    public static function send($templateSlug, $params){
        $templates=[
          'system-user-created'=>''
        ];
//        $template=EmailTemplate::findOne(['slug'=>$templateName]);

        if (!array_key_exists($templateSlug, $templates)) {
            throw new \Exception('EmailTemplate.send: '.$templateSlug.' missing');
        }
        $template=$templates[$templateSlug];
        if (self::sendSimpleEmail($templateSlug, $params['to'])){
            return true;
        } else {
            throw new \Exception('EmailTemplate.send: '.$templateSlug.' failed');
        }
    }

    protected static function sendSimpleEmail($slug, $to, $subject=null, $from=null){
        $message=\Yii::$app->mailer->compose('templates/'.$slug);
        $message->setTo($to);
        $message->setFrom($from?$from:\Yii::$app->params['adminEmail']);
        $message->setSubject($subject?$subject:$slug);
        $sent=$message->send();
        return $sent;
    }
    protected static function sendTemplatedEmail($template, $params){
        $merged=[
            'to'=>array_key_exists('to',$params) && $params['to']?$params['to']:\Yii::$app->params['logEmail'],
            'subject'=> $template->subject,
            'model'=> array_key_exists('tokenSource',$params)
                ?$params['tokenSource']
                :new \stdClass()
        ];
              //todo html and text views
//            [
//                'html' => $view,
//                'text' => $view.'-text',
//            ],
        $message=Yii::$app->mailer->compose('templates/'.$template->slug, $merged);
        if (array_key_exists('attachment',$params)){
            $message->attach($params['attachment']);
        }
        if(array_key_exists('cc',$params)){
            $message->setCc($params['cc']);
        }
        if(array_key_exists('bcc',$params)){
            $message->setBcc($params['bcc']);
        }
        $message->setTo($merged['to']);
        $message->setFrom(Yii::$app->params['adminEmail']);
        $message->setSubject($merged['subject']);
        $sent=$message->send();
        return $sent;
    }

    public static function csv($models){
        $csv='';
        foreach ($models as $model){
            $csv.=$model.', ';
        }
        return rtrim(trim($csv),',');
    }

    public static function yearMonthsDesc($startYear=null,$startMonth=null,$endYear=null,$endMonth=null){
        if ($endYear==null){
            $lastMonth=  strtotime('last month');
            $endMonth=(int)date('n', $lastMonth);
            $endYear=(int)date('Y', $lastMonth);
        }
        if ($startYear==null){
            $startYear=isset(\Yii::$app->params['fromYear'])?\Yii::$app->params['fromYear']:2017;
            $startMonth=isset(\Yii::$app->params['fromMonth'])?\Yii::$app->params['fromMonth']:1;
            $endMonth=(int)date('n', $lastMonth);
            $endYear=(int)date('Y', $lastMonth);
        }

        $yearMonths=[];
        for($year=$endYear;$year>=$startYear;$year--){
            $latestMonth=$year==$endYear?($endMonth?$endMonth:12):12;
            $earliestMonth=$year==$startYear?$startMonth:1;
            for($month=$latestMonth;$month>=$earliestMonth;$month--){
                $yearMonths[$year.'-'.$month]=$year.'-'.$month;
            }
        }
        return $yearMonths;
    }

    public static function dir($subDir=''){
        $dir=\Yii::getAlias('@common/files/'.$subDir);
        if (!is_dir($dir)){
            FileHelper::createDirectory($dir);
        }
        if (!StringHelper::endsWith($dir, '/')){
            $dir=$dir.'/';
        }
        return $dir;
    }

    // In PHP for path/file.ext
    // basename means file.ext
    // filename means file
    public static function fullPath($dirSource, $basename='basename.ext'){
        return self::dir($dirSource).$basename;
    }

    public static function daysSince($date){
        if (!$date) return false;
        $tsDate=\Yii::$app->formatter->asTimestamp($date);
        return  (time()-$tsDate)/(60*60*24);
    }

    public static function monthEnd($monthStart){
        $start=new \DateTime($monthStart);
        $end=$start->modify('+1 month');
        $end=$start->modify('-1 day');
        $monthEnd=$end->format('Y-m-d');
        return $monthEnd;
    }

    public static function roundUp($value, $int=1){
        return (int) ($value ? (ceil(intval($value) / $int) * $int) : 0);
    }

    public static function booleanDd($yes='Yes',$no='No') {
        return [1=>$yes,0=>$no];
    }
}

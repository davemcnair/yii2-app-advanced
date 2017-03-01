<?php
namespace backend\tests\Helper;

class Acceptance extends \Codeception\Module
{
    function select2nth($I, $id, $n){
        $I->click('#select2-casefile-'. $id .'-container');
        $I->wait(1);
        $I->seeElement('.select2-container--open li:nth-of-type('. $n .')');
        $I->click('.select2-container--open li:nth-of-type('. $n .')');
        $I->dontSeeElement('.select2-container--open li:nth-of-type('. $n .')');
    }

    function select2($I, $attr, $optionName){
        $id='#select2-'. $attr;
        $container=$id.'-container';
        $I->waitForElement($container);
        $I->click($container);
        $results=$id .'-results';
        $option=$results.' li[id$="-'.$optionName.'"]';
        $I->wait(1);
        $I->click($option);

    }

    function datePicker($I, $id, $n){
        $I->click($id .'-disp'); // Click to open date picker
        $I->waitForElement('.datepicker-dropdown'); // Wait until it's visible
        $I->click('.datepicker-dropdown .datepicker-days .table-condensed tbody tr td:nth-child('. $n .')'); // Click on a date
    }
}
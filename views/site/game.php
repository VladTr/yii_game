<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Game';

$js = <<<JS
    $('#btn').on('click', function(){
        $.ajax({
            url: 'index.php?r=site/game',
            type: 'Get',
            success: function(res){
                if (res.value) {
                    $('#result')[0].innerHTML = 'type: '+res.type +'<br>' + 'value: ' +res.value;
                } else {
                     $('#result')[0].innerHTML = 'error';
                }
                
            },
            error: function(){
                alert('Error!');
            }
        });
    });
JS;

$this->registerJs($js);


?>

<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        you can get award: money or score or valuable object
    </p>
    <button id="btn" class="btn btn-primary">
        press
    </button>
    <div id="result" class="border border-success">
        result:
    </div>

</div>

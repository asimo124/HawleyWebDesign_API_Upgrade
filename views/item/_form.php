<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Item */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'color', [
        'template' => "{label}\n<div class=\"d-flex align-items-center gap-2\">{input}<span class=\"color-preview\"></span></div>\n{hint}\n{error}",
    ])->textInput(['maxlength' => true, 'placeholder' => '#1de9b6', 'style' => 'max-width: 14rem']) ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="mt-4">
        <?= $this->render('_color_palette', ['inputId' => Html::getInputId($model, 'color')]) ?>
    </div>

</div>

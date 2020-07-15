<?php

use kartik\form\ActiveForm;

?>
<h1>Зарегистрировать новый номер</h1>
<?php
ActiveForm::begin(['method' => 'post', 'action' => 'reg-telegram']) ?>

        <input type="tel" placeholder="new number" name="number">
    <button type="submit">    Go</button>
    <?php ActiveForm::end() ?>



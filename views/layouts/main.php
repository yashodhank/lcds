<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\helpers\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="apple-touch-icon-precomposed" href="<?php echo Yii::$app->request->baseUrl; ?>/favicon-152.png">
    <link rel="shortcut icon" href="<?php echo Yii::$app->request->baseUrl; ?>/favicon.ico" type="image/x-icon" />
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<script type="text/javascript">window.jqReady = [];</script>
<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::t('app', 'LCDS'),
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => Yii::t('app', 'Devices'), 'url' => ['/device'], 'visible' => Yii::$app->user->can('setDevices')],
            ['label' => Yii::t('app', 'Templates'), 'url' => ['/screen-template'], 'visible' => Yii::$app->user->can('setTemplates')],
            ['label' => Yii::t('app', 'Screens'), 'url' => ['/screen'], 'visible' => Yii::$app->user->can('setScreens')],
            ['label' => Yii::t('app', 'Flows'), 'url' => ['/flow'], 'visible' => Yii::$app->user->can('setOwnFlowContent')],
            ['label' => Yii::t('app', 'Content'), 'url' => ['/content'], 'visible' => Yii::$app->user->can('setContent')],
            ['label' => Yii::t('app', 'Content types'), 'url' => ['/content-type'], 'visible' => Yii::$app->user->can('setContentTypes')],
            ['label' => Yii::t('app', 'Users'), 'url' => ['/user'], 'visible' => Yii::$app->user->can('admin')],
            Yii::$app->user->isGuest ? (
                ['label' => Yii::t('app', 'Login'), 'url' => ['/auth/login']]
            ) : (
                ['label' => Yii::t('app', 'Logout ({username})', ['username' => Yii::$app->user->identity->username]), 'url' => ['/auth/logout']]
            ),
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?php
        foreach (Alert::getAlerts() as $alert) {
            echo '<div class="alert alert-'.$alert['type'].'">'.$alert['message'].'</div>';
        } ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <a href="https://github.com/jf-guillou/">jf-guillou</a> <?= date('Y') ?> - <?= Yii::t('app', 'Developed for the {iut}', ['iut' => '<a href="https://iut-stbrieuc.univ-rennes1.fr/">IUT Saint-Brieuc</a>'])?></p>

        <p class="pull-right"><?= Yii::powered() ?> <a href="https://github.com/jf-guillou/lcds/">Github</a></p>
    </div>
</footer>

<?php $this->endBody() ?>
<script type="text/javascript">
if (window.hasOwnProperty('jqReady')) {
    $(function() {
        window.jqReady.forEach(function(f) {
            f();
        });
    });
}
</script>
</body>
</html>
<?php $this->endPage() ?>

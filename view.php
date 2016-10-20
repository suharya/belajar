<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use common\models\Provinsi;
use common\models\Kota;
use common\models\Rumahsakit;

use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\overlays\InfoWindow;
use dosamigos\google\maps\overlays\Marker;
use dosamigos\google\maps\Map;
use dosamigos\google\maps\services\GeocodingClient;
use dosamigos\google\maps\overlays\Polygon;
use dosamigos\google\maps\overlays\Circle;
use dosamigos\google\maps\overlays\CircleOptions;
use dosamigos\google\maps\overlays\PolylineOptions;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

<!DOCTYPE html>
<html>
<body>

<p>Click the button to get your coordinates.</p>

<button onclick="getLocation()">Get Location!</button>

<p id="demo"></p>

<script>
var x = document.getElementById("demo");

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(redirectToPosition);
    } else { 
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}

function redirectToPosition(position) {
    window.location='view.php?lat='+position.coords.latitude+'&lng='+position.coords.longitude;
}
</script>

</body>
</html>

<?php

$lat1=(isset($_GET['lat']))?$_GET['lat']:'';
$lng1=(isset($_GET['lng']))?$_GET['lng']:'';

$CONST_LAT = 0.530121;
$CONST_LNG = 123.059288;

$CONST_ZOOM = 5;
$CONST_TITLE = "Koordinat User Belum Terisi";
$CONST_CONTENT = "Nama & Alamat User Belum Terisi";
$CONST_TITLE_RS = "Koordinat RS Belum Terisi";
$CONST_CONTENT_RS = "Nama RS & Alamat RS Belum Tersedia";

$lat=(isset($_GET['lat']))?$_GET['lat']:'';
$lng=(isset($_GET['lng']))?$_GET['lng']:'';

$lat = (empty($model->lat)) ? $CONST_LAT : $model->lat;
$lng = (empty($model->lng)) ? $CONST_LNG : $model->lng;
$glat = (empty($geolat)) ? $CONST_LAT : $geolat;
$glng = (empty($geolng)) ? $CONST_LNG : $geolng;
$zoom = (empty($model->lat) OR empty($model->lng)) ? $CONST_ZOOM : 14;
$title = (empty($model->lat) OR empty($model->lng)) ? $CONST_TITLE : $model->username;
$content = (empty($model->lat) OR empty($model->lng)) ? $CONST_CONTENT : '<p>'.$model->username.'</br>'.$model->alamat.'</br>'.implode(\yii\helpers\ArrayHelper::map($model->kota, 'id','nama')).'</p>';

$rs =  new Rumahsakit();
$keterangan = '<p>'.ArrayHelper::getColumn(Rumahsakit::find()->all(),'nama_rs').'</br>'.ArrayHelper::getColumn(Rumahsakit::find()->all(),'alamat').'</br>'.ArrayHelper::getColumn(Rumahsakit::find()->all(),'kota_id').'</p>';
$titleRS = (empty(ArrayHelper::getColumn(Rumahsakit::find()->all(),'lat')) OR empty(ArrayHelper::getColumn(Rumahsakit::find()->all(),'lng'))) ? $CONST_TITLE_RS : ArrayHelper::getColumn(Rumahsakit::find()->all(),'nama_rs');
$contentRs = (empty(ArrayHelper::getColumn(Rumahsakit::find()->all(),'lat')) OR empty(ArrayHelper::getColumn(Rumahsakit::find()->all(),'lng'))) ? $CONST_CONTENT_RS : ArrayHelper::getColumn(Rumahsakit::find()->all(),'alamat');
$allconten = $contentRs1 . '</br>' . $contentRs1;

$coord1 = new LatLng(['lat' => $lat1, 'lng' => $lng1]);

$map = new Map([
    'center' => $coord1,
    'zoom' => $zoom,
    'width' => 1024,
    'height' => 512,
]);

$latlist = ArrayHelper::getColumn(Rumahsakit::find()->all(),'lat');
$lnglist = ArrayHelper::getColumn(Rumahsakit::find()->all(),'lng');

// Loop LatLng
$y = 0;
foreach ($latlist as $ltls) {
    $coordrs[$y] = new LatLng(['lat' => $latlist[$y], 'lng' => $lnglist[$y] ]);
    $y++;
}

// Loop Marker
$x = 0;
foreach ($coordrs as $cors) {
    $marker1[$x] = new Marker([
        'position' => $coordrs[$x],
        'title' => $titleRS[$x],
    ]);

    $marker1[$x]->attachInfoWindow(
        new InfoWindow([
            'content' => $contentRs[$x]
        ])
    );

    // Add marker to the map
    $map->addOverlay($marker1[$x]);
    $x++;
}

// Lets add a marker now
$marker = new Marker([
    'position' => $coord,
    'title' => $title,
]);


// Provide a shared InfoWindow to the marker
$marker->attachInfoWindow(
    new InfoWindow([
        'content' => $content,
    ])
);

// Add marker to the map
$map->addOverlay($marker);

$coord = new LatLng(['lat' => $lat, 'lng' => $lng]);

$circle = new Circle([
    'center' => $coord,
    'radius' => 10000,
    'strokeColor' => "#0000FF",
    'strokeOpacity' => 0.8,
    'strokeWeight' => 2,
    'fillColor' => "#0000FF",
    'fillOpacity' => 0.4
]);
 
// Add it now to the map
$map->addOverlay($circle);

echo "<p>";
echo $map->display();
echo "</p>"
?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            
            // 'id',
            //'username',
            ['label' => 'Nama Pengguna','value' => $model->username],
            'nama_lengkap',
            'alamat:ntext',
            //'provinsi_id',
            //['label' => 'Provinsi','value' => $model->provinsi_id],
           ['label' => 'Provinsi','value' => implode(\yii\helpers\ArrayHelper::map($model->provinsi, 'id','nama'))],
            //'kota_id',
            ['label' => 'Kota/Kabupaten','value' => implode(\yii\helpers\ArrayHelper::map($model->kota, 'id','nama'))],
            //'kecamatan_id',
            ['label' => 'Kecamatan','value' => implode(\yii\helpers\ArrayHelper::map($model->kecamatan, 'id','nama'))],
            //'kelurahan_id',
            ['label' => 'Kelurahan','value' => implode(\yii\helpers\ArrayHelper::map($model->kelurahan, 'id','nama'))],
            'kode_pos',
            // 'lat',
            // 'lng',
            // 'auth_key',
            // 'password_hash',
            // 'password_reset_token',
            'email:email',
            // 'status',
            // 'created_at',
            // 'updated_at',
            
        ],
    ])?>

    <h3>ASURANSI TERDAFTAR</h3>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
            'label' => 'Nama Asuransi',
            'format' => 'raw',
            'value' => '<ul>' . $model->namaAsuransi . '</ul>', // function getNamaAsuransi in $model
            ]
        ],
    ]) ?>

</div>

<?php
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */

require 'lib/idiorm.php';
require '_config.php';
include_once "lib/ezsql/shared/ez_sql_core.php";
include_once "lib/ezsql/mysql/ez_sql_mysql.php";

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new \Slim\Slim();

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, and `Slim::delete`
 * is an anonymous function.
 */

/*
 pull in recipie data
 mix with food sources
 output map to closest locations for ingredients
 http://api.yummly.com/v1/api/recipe/Hot-Turkey-Salad-Sandwiches-Allrecipes?_app_id={YOUR APP ID}&_app_key={YOUR API KEY}
 p
 */

// GET route
$app->get('/', function () use ($app) {

    $app->render('_header.php',array());

    //http://api.punchfork.com/recipes?key=59d19fbfe48734fb&{optional-params}
    //http://www.recipepuppy.com/api/?i=onions,garlic&q=omelet
    //
    $app->render('welcome.php',array());
    $app->render('_footer.php',array());

});

$app->get('/json', function () use ($app) {
    header('Content-type: application/json');
    $db = new ezSQL_mysql('hackvt','hackvt2012','hackvt','localhost');
    if(isset($_GET['city'])){
        $city = urlencode($_GET['city']);
        $geocodejson = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . $city . '&sensor=false');
        $geoloc = json_decode($geocodejson);
        $lat = round($geoloc->results[0]->geometry->location->lat, 3);
        $long = round($geoloc->results[0]->geometry->location->lng, 3);
    } else {
        $lat = $db->escape(round($_GET['latitude'], 3));
        $long = $db->escape(round($_GET['longitude'], 3));
    };
    if(isset($_GET['search'])) {
        $productsearch = $_GET['search'];
    } else {
        $productsearch = 'garlic';
    };
    $prodarray = array();

    $searcharray = explode(',', $productsearch);
    foreach($searcharray as $searchitem){
        $products = ORM::for_table('products')->where_like('product_title', '%' . $searchitem . '%')->find_many();
        foreach($products as $product){
            $prodarray[] = $product->id;
        };
    }

    
    $sqlquery = "SELECT id, title, latitude, longitude, ( 3959 * acos( cos( radians(" . $lat . ") ) * cos( radians( latitude ) )   * cos( radians( longitude ) - radians(" . $long . ") ) + sin( radians(" . $lat . ") ) * sin( radians( latitude ) ) ) ) AS distance FROM farms HAVING distance < 15 ORDER BY distance;";
    $farms = $db->get_results($sqlquery);
    $outputfarms = array();

    foreach($farms as $farm){
        if(count($prodarray) > 1){
            $product2farm = ORM::for_table('product2farm')->where('farm_id', $farm->id)->where_in('product_id', $prodarray)->find_many();
        } else {
            $product2farm = ORM::for_table('product2farm')->where('farm_id', $farm->id)->where('product_id', $prodarray)->find_many();
        };
        if($product2farm){
            $outputfarms[$farm->id] = array(
                'title' => $farm->title,
                'latitude' => $farm->latitude,
                'longitude' => $farm->longitude,
                'distance' => round($farm->distance)
            );

            foreach($product2farm as $item){
                $foundproduct = ORM::for_table('products')->find_one($item->product_id);

                $outputfarms[$farm->id]['product'] = $foundproduct->product_title;
            };
            //var_dump($product2farm);
        };
    };
    echo json_encode($outputfarms);
});

$app->get('/m', function () use ($app) {
    $app->render('mobile.html',array());
});


// POST route
$app->post('/post', function () {
    echo 'This is a POST route';
});

// PUT route
$app->put('/put', function () {
    echo 'This is a PUT route';
});

// DELETE route
$app->delete('/delete', function () {
    echo 'This is a DELETE route';
});

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();

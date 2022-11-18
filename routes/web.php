<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (\Illuminate\Http\Request $request) {
    $server = $_SERVER['REMOTE_ADDR'];
    $rrs = file_get_contents("http://api.sypexgeo.net/json/".$server);
    $obj = json_decode($rrs);
    $sentence = null;
    if ($obj->city){
        $sentence = \App\Models\City::where('slug', $obj->city->name_en)->first();
    } else if($obj->region) {
        $sentence = \App\Models\Region::whereBetween('code', explode(',', str_replace(' ', '', $obj->region->auto)))->first();
    }
    $main_domain = parse_url(env('APP_URL'))['host'];
    $domain = parse_url($request->url())['host'];
    $slug = str_replace(".$main_domain", '', $domain);
    $place = \App\Models\Area::where('slug', $slug)->first()??\App\Models\Region::where('slug', $slug)->first()??\App\Models\City::where('slug', $slug)->first();
    if ($place->variable??false) $place->variable = json_decode($place->variable);
    return \Inertia\Inertia::render('home', ['place' => $place, 'sentence'=>$sentence])->withViewData(['place' => $place]);
});
Route::post('/search-place', function (\Illuminate\Http\Request $request) {
    $q = "%{$request->get('q')}%";
    $result =
        \App\Models\Area::orWhere('slug', 'LIKE', $q)
            ->orWhere('name', 'LIKE', $q)
            ->orWhere('variable', 'LIKE', $q)
            ->take(10)
            ->get()
            ->map(fn($el) => array_merge($el->only(['slug', 'name']), ['type' => 'area', 'variable' => json_decode($el->variable)]))
            ->toArray();
    if (count($result) < 5) {
        $result = array_merge(
            $result,
            \App\Models\Region::orWhere('slug', 'LIKE', $q)
                ->orWhere('name', 'LIKE', $q)
                ->orWhere('variable', 'LIKE', $q)
                ->take(10)
                ->get()
                ->map(fn($el) => array_merge($el->only(['slug', 'name']), ['type' => 'region', 'variable' => json_decode($el->variable)]))
                ->toArray()
        );
    }
    if (count($result) < 5) {
        $result = array_merge(
            $result,
            \App\Models\City::orWhere('slug', 'LIKE', $q)
                ->orWhere('name', 'LIKE', $q)
                ->orWhere('variable', 'LIKE', $q)
                ->take(10)
                ->get()
                ->map(fn($el) => array_merge($el->only(['slug', 'name']), ['type' => 'city', 'variable' => json_decode($el->variable)]))
                ->toArray()
        );
    }
    return response($result);
});

Route::get('change-place/{type}/{slug}', function ($type, $slug) {
    $url = parse_url(env('APP_URL'));

    $domain = $url['host'].(isset($url['port'])?':'.$url['port']:'');
    return redirect()->to("{$url['scheme']}://{$slug}.{$domain}");
});

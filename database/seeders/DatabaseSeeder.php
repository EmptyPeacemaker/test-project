<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Area;
use App\Models\City;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        foreach (json_decode(Storage::get('areas.json')) as $el) {
            $slug = $el->slug;
            $name = $el->name;
            unset ($el->slug, $el->name);
            Area::updateOrCreate(['slug'=>$slug], [
                'name' => $name,
                'variable' => json_encode($el)
            ]);
        }
        foreach (json_decode(Storage::get('region.json')) as $el) {
            $slug = $el->slug;
            $name = $el->name;
            $code = $el->code;
            $area = $el->area;
            unset ($el->slug, $el->name, $el->code, $el->area);
            $area_id = Area::where('slug', $area)->first()->id;
            Region::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'variable' => json_encode($el),
                'areas_id' => $area_id,
                'code' => $code
            ]);
        }
        foreach (json_decode(Storage::get('city.json')) as $el) {
            $slug = $el->slug;
            $name = $el->name;
            $region = $el->region;
            unset ($el->slug, $el->name, $el->region);
            $region_id = Region::where('code', $region)->first()->id;
            City::updateOrCreate(['slug'=>$slug], [
                'name' => $name,
                'variable' => json_encode($el),
                'region_id' => $region_id
            ]);
        }
    }
}

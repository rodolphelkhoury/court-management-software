<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use App\Models\{
    Complex, Court, CourtType, Image, SurfaceType
};
use App\Domain\CourtType\Enums\CourtTypeName;
use App\Domain\SurfaceType\Enums\SurfaceTypeName;
 
class CourtSeeder extends Seeder
{
    public function run(): void
    {
        // Manually load Faker (installed separately to avoid PHP version conflicts)
        require_once base_path('vendor/fakerphp/faker/src/autoload.php');
        $faker = \Faker\Factory::create();
        
        $allowedDurations = [0.5, 0.75, 1.0, 1.25, 1.5, 1.75, 2.0];
 
        /** -----------------------------------------
         *  PRELOAD TYPES
         * ----------------------------------------*/
        $courtTypes = CourtType::pluck('id', 'name');
        $surfaceTypes = SurfaceType::pluck('id', 'name');
 
        /** -----------------------------------------
         *  COMPLEXES (Lebanon only)
         * ----------------------------------------*/
        $complexData = [
            [
                'name' => 'Phoenix Sports Complex – Dekwaneh',
                'description' => 'A modern multi-sport center located in the heart of Dekwaneh with indoor and outdoor facilities.',
                'image' => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20144217.png',
                'lat' => 33.89244, 'lng' => 35.54401,
            ],
            [
                'name' => 'Summit Athletic Center – Antelias',
                'description' => 'Premium athletic venue offering basketball, padel, and tennis training.',
                'image' => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20144217.png',
                'lat' => 33.91312, 'lng' => 35.57992,
            ],
            // Add the rest...
        ];
 
        $complexes = collect();
 
        foreach ($complexData as $cx) {
            $created = Complex::create([
                'company_id' => 1,
                'name' => $cx['name'],
                'description' => $cx['description'],
                'line1' => '123 Main St', // static address
                'line2' => 'Floor 2', // static secondary
                'city' => 'Lebanon',
                'country' => 'Lebanon',
                'latitude' => $cx['lat'],
                'longitude' => $cx['lng'],
            ]);
 
            Image::create([
                'disk' => 'public',
                'name' => 'primary',
                'filepath' => $cx['image'],
                'mimetype' => 'png',
                'width' => 500,
                'height' => 300,
                'filesize' => 500,
                'owner_type' => Complex::class,
                'owner_id' => $created->id,
            ]);
 
            $complexes->push($created);
        }
 
        /** -----------------------------------------
         *  HELPER TO CREATE COURTS
         * ----------------------------------------*/
        $createCourt = function (array $data, $complex) use ($allowedDurations) {
 
            $court = Court::create([
                'complex_id' => $complex->id,
                'court_type_id' => $data['court_type_id'],
                'surface_type_id' => $data['surface_type_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'hourly_rate' => $data['hourly_rate'],
                'divisible' => rand(0, 1),
                'max_divisions' => rand(1, 2),
                'opening_time' => '08:00:00',
                'closing_time' => '23:00:00',
                'reservation_duration' => $allowedDurations[array_rand($allowedDurations)],
                'longitude' => $complex->longitude + (rand(-2, 2) * 0.001), // small random offset
                'latitude'  => $complex->latitude + (rand(-2, 2) * 0.001),
                'address_line' => $complex->name,
            ]);
 
            Image::create([
                'disk' => 'public',
                'name' => 'primary',
                'filepath' => $data['image'],
                'mimetype' => 'png',
                'width' => 500,
                'height' => 200,
                'filesize' => 500,
                'owner_type' => Court::class,
                'owner_id' => $court->id
            ]);
        };
 
        /** -----------------------------------------
         *  COURTS PER COMPLEX (3–5 each)
         * ----------------------------------------*/
        $courtLibrary = [
            [
                'type' => CourtTypeName::BASKETBALL->value,
                'surface' => SurfaceTypeName::HARD_ACRYLIC->value,
                'name' => 'Arena Court', 'rate' => 18,
                'desc' => 'Indoor basketball court with pro lighting.',
                'img'  => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20141645.png'
            ],
            [
                'type' => CourtTypeName::SOCCER->value,
                'surface' => SurfaceTypeName::GRASS_SYNTHETIC->value,
                'name' => 'Elite Turf Field', 'rate' => 22,
                'desc' => 'Full-sized synthetic soccer field.',
                'img'  => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20141645.png'
            ],
            // add more...
        ];
 
        foreach ($complexes as $complex) {
            $courtsToGenerate = rand(3, 5);
 
            for ($i = 0; $i < $courtsToGenerate; $i++) {
                $template = $courtLibrary[array_rand($courtLibrary)];
 
                $createCourt([
                    'name' => $template['name'] . " #" . ($i + 1),
                    'description' => $template['desc'],
                    'hourly_rate' => $template['rate'],
                    'court_type_id' => $courtTypes[$template['type']],
                    'surface_type_id' => $surfaceTypes[$template['surface']],
                    'image' => $template['img'],
                ], $complex);
            }
        }
    }
}
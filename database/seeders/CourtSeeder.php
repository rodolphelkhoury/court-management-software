<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    Complex, Court, CourtType, Image, SurfaceType
};
use App\Domain\CourtType\Enums\CourtTypeName;
use App\Domain\SurfaceType\Enums\SurfaceTypeName;
use Faker\Factory as Faker;

class CourtSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

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
            [
                'name' => 'Greenfield Sports Hub – Baabdat',
                'description' => 'A nature-surrounded sports hub with high-quality turf and open-air courts.',
                'image' => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20144217.png',
                'lat' => 33.93142, 'lng' => 35.67111,
            ],
            [
                'name' => 'Metro Active Arena – Sin el Fil',
                'description' => 'Indoor and rooftop courts designed for fast-paced games and professional matches.',
                'image' => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20144217.png',
                'lat' => 33.88171, 'lng' => 35.52894,
            ],
            [
                'name' => 'Horizon Sports Village – Jounieh',
                'description' => 'A wide sports village offering football, tennis and basketball courts.',
                'image' => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20144217.png',
                'lat' => 33.97212, 'lng' => 35.62981,
            ],
            [
                'name' => 'Cedars Court Center – Zalka',
                'description' => 'A premium indoor courts center with advanced lighting and modern amenities.',
                'image' => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20144217.png',
                'lat' => 33.89452, 'lng' => 35.54811,
            ],
            [
                'name' => 'North Star Sports Arena – Tripoli',
                'description' => 'The top sports destination in North Lebanon with multiple training fields.',
                'image' => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20144217.png',
                'lat' => 34.43901, 'lng' => 35.83410,
            ],
            [
                'name' => 'Legacy Sports Dome – Hazmieh',
                'description' => 'A modern dome complex with indoor basketball, futsal, and tennis.',
                'image' => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20144217.png',
                'lat' => 33.85831, 'lng' => 35.52941,
            ],
            [
                'name' => 'Golden Field Club – Mansourieh',
                'description' => 'A recently renovated sports center with premium synthetic turf courts.',
                'image' => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20144217.png',
                'lat' => 33.85412, 'lng' => 35.56758,
            ],
            [
                'name' => 'Mirage Sports Park – Broummana',
                'description' => 'Open-air courts surrounded by mountain views. Ideal for tournaments.',
                'image' => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20144217.png',
                'lat' => 33.89601, 'lng' => 35.62771,
            ],
        ];

        $complexes = collect();

        foreach ($complexData as $cx) {
            $created = Complex::create([
                'company_id' => 1,
                'name' => $cx['name'],
                'description' => $cx['description'],
                'line1' => $faker->streetAddress,
                'line2' => $faker->secondaryAddress,
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
        $createCourt = function (array $data, $complex) use ($allowedDurations, $faker) {

            $court = Court::create([
                'complex_id' => $complex->id,
                'court_type_id' => $data['court_type_id'],
                'surface_type_id' => $data['surface_type_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'hourly_rate' => $data['hourly_rate'],
                'divisible' => rand(0,1),
                'max_divisions' => rand(1,4),
                'opening_time' => '08:00:00',
                'closing_time' => '23:00:00',
                'reservation_duration' => $faker->randomElement($allowedDurations),
                'longitude' => $complex->longitude + ($faker->randomFloat(5, -0.002, 0.002)),
                'latitude'  => $complex->latitude + ($faker->randomFloat(5, -0.002, 0.002)),
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

            // Basketball
            [
                'type' => CourtTypeName::BASKETBALL->value, 'surface' => SurfaceTypeName::HARD_ACRYLIC->value,
                'name' => 'Arena Court', 'rate' => 18,
                'desc' => 'Indoor basketball court with pro lighting.',
                'img'  => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20141645.png'
            ],

            // Soccer
            [
                'type' => CourtTypeName::SOCCER->value, 'surface' => SurfaceTypeName::GRASS_SYNTHETIC->value,
                'name' => 'Elite Turf Field', 'rate' => 22,
                'desc' => 'Full-sized synthetic soccer field.',
                'img'  => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20141645.png'
            ],

            // Padel
            [
                'type' => CourtTypeName::PADEL->value, 'surface' => SurfaceTypeName::SYNTHETIC_RUBBER->value,
                'name' => 'Padel Pro Court', 'rate' => 14,
                'desc' => 'Modern padel court with panoramic walls.',
                'img'  => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20141645.png'
            ],

            // Tennis
            [
                'type' => CourtTypeName::TENNIS->value, 'surface' => SurfaceTypeName::HARD_ACRYLIC->value,
                'name' => 'Grand Slam Court', 'rate' => 20,
                'desc' => 'Professional-grade tennis court.',
                'img'  => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20141645.png'
            ],

            // Volleyball
            [
                'type' => CourtTypeName::VOLLEYBALL->value, 'surface' => SurfaceTypeName::HARD_ASPHALT->value,
                'name' => 'Volley Zone', 'rate' => 12,
                'desc' => 'Training-friendly volleyball ground.',
                'img'  => 'https://ik.imagekit.io/rqey376md/Screenshot%202025-12-05%20141645.png'
            ],
        ];


        /** -----------------------------------------
         *  ASSIGN 3–5 COURTS TO EACH COMPLEX
         * ----------------------------------------*/
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

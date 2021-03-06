<?php

use Illuminate\Database\Seeder;
use App\Representante;
use App\Usuario;
use App\Localidad;
use Faker\Factory as Faker;

class RepresentanteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('es_ES');
        $localidades = Localidad::whereNotNull('localidad_id')->get()->toArray();
        for ($i=0; $i<=25; $i++)
        {
            $genderRand = rand(0, 1);
            $name = $faker->firstName($gender = ($genderRand==1?'female':'male'));
            $l_name = $faker->lastName;
            $representate = Representante::create(array(
                'cedula' => $faker->unique()->numberBetween($min = 9000000, $max=24000000),
                'nombre' => $name,
                'apellido' => $l_name,
                'genero' => ( $genderRand==1 ? 'F' : 'M'),
                'fecha_nacimiento' => $faker->date($format = 'Y-m-d', $max = '-16 years'),
                'telefono' => $faker->e164PhoneNumber,
                'direccion' => $faker->address,
                'localidad_id' => $localidades[array_rand($localidades)]['id']
            ));

            Usuario::create([
                'usuario' => $name.'_'.$l_name.'_'.rand(1, 2000),
                'contrasena' => bcrypt(123456),
                'rol_type' => 'App\Representante',
                'correo' => $faker->freeEmail,
                'rol_id' => $representate->id
            ]);
        }
    }
}

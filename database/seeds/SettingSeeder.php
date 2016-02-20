<?php

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$skills = \App\Models\SDE\InvType::
			    where('typeName', 'LIKE', '%Processing')
			->orWhere('typeName', 'LIKE', '%Efficiency')
			->orderBy('typeName', 'ASC')
			->get();

		foreach ($skills as $skill) {
			\App\Models\Setting::create([
				'key'   => $skill->typeName,
				'value' => 0,
			]);
		}
	}
}

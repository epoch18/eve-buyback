<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutpostsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('api_outposts')) {
			return;
		}

		Schema::create('api_outposts', function (Blueprint $table)
		{
			$table->integer   ('stationID'      )->unsigned()->primary();
			$table->string    ('stationName'    );
			$table->integer   ('stationTypeID'  )->unsigned();
			$table->integer   ('solarSystemID'  )->unsigned();
			$table->integer   ('corporationID'  )->unsigned();
			$table->string    ('corporationName');
			$table->bigInteger('x');
			$table->bigInteger('y');
			$table->bigInteger('z');

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if (Schema::hasTable('api_outposts')) {
			Schema::drop('api_outposts');
		}
	}
}

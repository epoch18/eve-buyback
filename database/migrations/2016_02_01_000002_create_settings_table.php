<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('buyback_settings')) {
			return;
		}

		Schema::create('buyback_settings', function (Blueprint $table)
		{
			$table->string('key'  )->primary();
			$table->text  ('value');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if (Schema::hasTable('buyback_settings')) {
			Schema::drop('buyback_settings');
		}
	}
}

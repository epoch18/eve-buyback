<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function (Blueprint $table)
		{
			$table->increments('userID'            )->unsigned();
			$table->integer   ('characterID'       )->unsigned();
			$table->string    ('characterName'     );
			$table->string    ('characterOwnerHash');
			$table->integer   ('corporationID'     )->unsigned();
			$table->string    ('corporationName'   );
			$table->string    ('corporationTicker' );
			$table->integer   ('allianceID'        )->unsigned();
			$table->string    ('allianceName'      );
			$table->string    ('allianceTicker'    );
			$table->integer   ('flags'             )->unsigned()->default(0);

			$table->rememberToken();
			$table->timestamps();
			$table->softDeletes();

			$table->unique(['characterID', 'characterOwnerHash',], 'character');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractItemsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('api_contract_items')) {
			return;
		}

		Schema::create('api_contract_items', function (Blueprint $table)
		{
			$table->integer('recordID'   )->unsigned()->primary();
			$table->integer('contractID' )->unsigned();
			$table->integer('typeID'     )->unsigned();
			$table->integer('quantity'   );
			$table->integer('rawQuantity');
			$table->integer('singleton'  );
			$table->integer('included'   );

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
		if (Schema::hasTable('api_contract_items')) {
			Schema::drop('api_contract_items');
		}
	}
}

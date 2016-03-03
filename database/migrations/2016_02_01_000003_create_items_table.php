<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('buyback_items')) {
			return;
		}

		Schema::create('buyback_items', function (Blueprint $table)
		{
			$table->integer('typeID'      )->unsigned()->primary();
			$table->boolean('buyRaw'      );
			$table->boolean('buyRecycled' );
			$table->boolean('buyRefined'  );
			$table->double ('buyModifier' );
			$table->double ('buyPrice'    );
			$table->boolean('sell'        );
			$table->double ('sellModifier');
			$table->double ('sellPrice'   );
			$table->boolean('lockPrices'  )->default(false);

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
		if (Schema::hasTable('buyback_items')) {
			Schema::drop('buyback_items');
		}
	}
}

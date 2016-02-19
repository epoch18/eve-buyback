<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('api_contracts')) {
			return;
		}

		Schema::create('api_contracts', function (Blueprint $table)
		{
			$table->integer ('contractID'    )->unsigned()->primary();
			$table->integer ('issuerID'      )->unsigned();
			$table->integer ('issuerCorpID'  )->unsigned();
			$table->integer ('assigneeID'    )->unsigned();
			$table->integer ('acceptorID'    )->unsigned();
			$table->integer ('startStationID')->unsigned();
			$table->integer ('endStationID'  )->unsigned();
			$table->string  ('type'          );
			$table->string  ('status'        );
			$table->string  ('title'         );
			$table->boolean ('forCorp'       );
			$table->integer ('availability'  );
			$table->datetime('dateIssued'    );
			$table->datetime('dateExpired'   );
			$table->datetime('dateAccepted'  );
			$table->integer ('numDays'       );
			$table->datetime('dateCompleted' );
			$table->double  ('price'         );
			$table->double  ('reward'        );
			$table->double  ('collateral'    );
			$table->double  ('buyout'        );
			$table->double  ('volume'        );

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
		if (Schema::hasTable('api_contracts')) {
			Schema::drop('api_contracts');
		}
	}
}

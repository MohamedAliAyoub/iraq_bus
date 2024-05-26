<?php

namespace App\Console\Commands;

use App\Models\DriverFinancial;
use App\Models\DriverMoney;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateDriverMoneyStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    /**
     * The console command description.
     *
     * @var string
     */
    protected $signature = 'driver_money:update';
    protected $description = 'Update driver money status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get records created more than 48 hours ago
        $records = DriverMoney::where('created_at', '<=', Carbon::now()->subHours(48))
            ->where('status', 0)
            ->get();

        foreach ($records as $record) {

            DriverFinancial::where('driver_id', $record->driver_id)
                ->update([
                    'current_balance' => DB::raw('current_balance + ' . $record->price),
                    'suspended_balance' => DB::raw('suspended_balance - ' . $record->price)
                ]);
            $record->update(['status' => 1]);
        }

        $this->info('Driver money records updated from  suspended_balance to  current_balance successfully.');
    }
}

<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Terminal;
use Carbon\Carbon;

class ValidateWsToken extends Command
{
    protected $signature = 'run:validate-ws-token {token}';
    protected $description = 'Validate WebSocket token and check expiration';

    public function handle(): int
    {
        $token = $this->argument('token');

        $terminal = Terminal::where('token', $token)->first();

        if (!$terminal) {
            $this->error('Token not found.');
            return Command::FAILURE;
        }

        if($terminal->state == "off") {
            $this->error('Terminal is not running.');
            return Command::FAILURE;
        }

        $this->info('Token valid.');
        return Command::SUCCESS;
    }
}